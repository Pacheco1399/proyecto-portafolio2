<?php

namespace App\Core\Database;

use InvalidArgumentException;
use PDO;
use App\Utils\TextUtils;

/**
 * Sacado de Pulse Pricing
 */
class SelectQueryBuilder
{
    const ALLOWED_JOINS = ['JOIN', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'OUTER JOIN'];
    const ALLOWED_ORDERS = ['ASC', 'DESC'];

    protected const LIMIT_PARAM_NAME = ':stmt_limit';
    protected const OFFSET_PARAM_NAME = ':stmt_offset';

    protected array $selects = [];

    protected array $joins = [];

    protected array $wheres = [];

    protected array $groupBys = [];

    protected array $orderBys = [];

    protected array $params = [];

    protected ?int $limit;

    protected ?int $offset;

    public function __construct(
        protected string $from,
        protected string $tableAlias,
        // Selecciona todo lo de la tabla
        ?string $initialSelect = '*',
    ) {
        if ($initialSelect) {
            $this->selects[] = "$tableAlias.$initialSelect";
        }
    }

    public function select(string ...$selection): self
    {
        // Sobreescribe todos los SELECTs anteriores
        $this->selects = $selection;

        return $this;
    }

    public function addSelect(string $selection): self
    {
        // No agrega el select si ya existe, para disminuir la cantidad de
        // columnas que se cargan de la BD
        if (in_array($selection, $this->selects)) {
            return $this;
        }

        $this->selects[] = $selection;

        return $this;
    }

    public function join(
        string $table,
        string $alias,
        string $on,
        array $params = [],
        string $joinType = 'JOIN',
    ): self {
        $joinType = strtoupper($joinType);

        // Valida que no se pase un JOIN inválido
        $this->validateJoinType($joinType);

        $joinSql = "$joinType $table AS $alias ON ($on)";

        // No permite que se sobreescriba un JOIN distinto con el mismo alias
        if (isset($this->joins[$alias]) && $this->joins[$alias] !== $joinSql) {
            throw new InvalidArgumentException(
                sprintf(
                    'Ya existe un JOIN con el alias %s!',
                    $alias
                )
            );
        }

        $this->joins[$alias] = $joinSql;

        if ($params) {
            $this->params = array_merge($this->params, $params);
        }

        return $this;
    }

    public function innerJoin(
        string $table,
        string $alias,
        string $on,
        array $params = [],
    ): self {
        return $this->join($table, $alias, $on, $params, 'INNER JOIN');
    }

    public function leftJoin(
        string $table,
        string $alias,
        string $on,
        array $params = [],
    ): self {
        return $this->join($table, $alias, $on, $params, 'LEFT JOIN');
    }

    public function rightJoin(
        string $table,
        string $alias,
        string $on,
        array $params = [],
    ): self {
        return $this->join($table, $alias, $on, $params, 'RIGHT JOIN');
    }

    public function outerJoin(
        string $table,
        string $alias,
        string $on,
        array $params = [],
    ): self {
        return $this->join($table, $alias, $on, $params, 'OUTER JOIN');
    }

    public function andWhere(string $condition, array $params = []): self
    {
        $this->wheres[] = $condition;
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function andWhereEqual(
        string $column,
        $value,
        ?int $type = null,
        bool $negate = false,
    ): self {
        // Placeholder generado a partir del nombre de la columna
        $placeholder = ':'
            . TextUtils::slugify($column, '_')
            . ($negate ? '_not_equal_to' : '_equal_to');

        if ($type) {
            $value = new Param($value, $type);
        }

        $operator = $negate ? '!=' : '=';

        $this->andWhere("$column $operator $placeholder", [$placeholder => $value]);

        return $this;
    }

    public function andWhereNotEqual(string $column, $value, ?int $type = null): self
    {
        return $this->andWhereEqual($column, $value, $type, true);
    }

    public function andWhereIn(
        string $column,
        array $values,
        bool $negate = false,
    ): self {
        [$placeholders, $params] = $this->generateInPlaceholders(
            TextUtils::slugify($column, '_'),
            $values,
            $negate ? '_not_in' : '_in'
        );

        $operator = $negate ? 'NOT IN' : 'IN';

        $this->andWhere("$column $operator ($placeholders)", $params);

        return $this;
    }

    public function andWhereNotIn(string $column, array $values): self
    {
        return $this->andWhereIn($column, $values, true);
    }

    public function andWhereLike(
        string $column,
        $value,
        bool $valueFirst = false,
        bool $negate = false,
    ): self {
        // Placeholder generado a partir del nombre de la columna
        $placeholder = ':'
            . TextUtils::slugify($column, '_')
            . ($negate ? '_not_like' : '_like');

        $operator = $negate ? 'NOT LIKE' : 'LIKE';

        if ($valueFirst) {
            $this->andWhere("$placeholder $operator $column", [$placeholder => $value]);
        } else {
            $this->andWhere("$column $operator $placeholder", [$placeholder => $value]);
        }

        return $this;
    }

    public function andWhereNotLike(
        string $column,
        $value,
        bool $valueFirst = false,
    ): self {
        return $this->andWhereLike($column, $value, $valueFirst, true);
    }

    public function andWhereRlike(
        string $column,
        $value,
        bool $valueFirst = false,
        bool $negate = false,
    ): self {
        // Placeholder generado a partir del nombre de la columna
        $placeholder = ':'
            . TextUtils::slugify($column, '_')
            . ($negate ? '_not_rlike' : '_rlike');

        $operator = $negate ? 'NOT RLIKE' : 'RLIKE';

        if ($valueFirst) {
            $this->andWhere("$placeholder $operator $column", [$placeholder => $value]);
        } else {
            $this->andWhere("$column $operator $placeholder", [$placeholder => $value]);
        }

        return $this;
    }

    public function andWhereNotRlike(
        string $column,
        $value,
        bool $valueFirst = false,
    ): self {
        return $this->andWhereRlike($column, $value, $valueFirst, true);
    }

    public function groupBy(string ...$criterias): self
    {
        // Sobreescribe todos los GROUP BY anteriores
        $this->groupBys = $criterias;

        return $this;
    }

    public function addGroupBy(string $criteria): self
    {
        if (in_array($criteria, $this->groupBys)) {
            return $this;
        }

        $this->groupBys[] = $criteria;

        return $this;
    }

    public function orderBy(string $criteria, string $order): self
    {
        $order = strtoupper($order);

        // Valida que no se pase un orden inválido
        // NOTA: $criteria debería pasar por una lista blanca primero
        $this->validateOrder($order);

        // Sobreescribe todos los ORDER BY anteriores
        $this->orderBys = ["$criteria $order"];

        return $this;
    }

    public function addOrderBy(string $criteria, string $order): self
    {
        $order = strtoupper($order);

        // Valida que no se pase un orden inválido
        // NOTA: $criteria debería pasar por una lista blanca primero
        $this->validateOrder($order);

        $orderBy = "$criteria $order";

        if (in_array($orderBy, $this->orderBys)) {
            return $this;
        }

        $this->orderBys[] = $orderBy;

        return $this;
    }

    public function limit(?int $limit): self
    {
        $this->limit = $limit;

        if ($limit === null) {
            unset($this->params[self::LIMIT_PARAM_NAME]);
        } else {
            $this->params[self::LIMIT_PARAM_NAME] = new Param($limit, PDO::PARAM_INT);
        }

        return $this;
    }

    public function offset(?int $offset): self
    {
        $this->offset = $offset;

        if ($offset === null) {
            unset($this->params[self::OFFSET_PARAM_NAME]);
        } else {
            $this->params[self::OFFSET_PARAM_NAME] = new Param($offset, PDO::PARAM_INT);
        }

        return $this;
    }

    public function paginate(int $itemsPerPage, int $page): self
    {
        $this
            ->limit($itemsPerPage)
            ->offset(($page - 1) * $itemsPerPage);

        return $this;
    }

    public function build(): array
    {
        $builtSelect = implode(',', $this->selects);
        $builtFrom = "$this->from AS $this->tableAlias";
        $builtJoins = implode("\n", $this->joins);
        $builtWhere = empty($this->wheres)
            ? ''
            : "WHERE\n\t" . implode("\n\tAND ", $this->wheres);
        $builtGroupBy = empty($this->groupBys)
            ? ''
            : 'GROUP BY ' . implode(', ', $this->groupBys);
        $builtOrderBy = empty($this->orderBys)
            ? ''
            : 'ORDER BY ' . implode(', ', $this->orderBys);

        if (isset($this->offset, $this->limit)) {
            $builtLimit = 'LIMIT ' . self::OFFSET_PARAM_NAME . ', ' . self::LIMIT_PARAM_NAME;
        } elseif (isset($this->limit)) {
            $builtLimit = 'LIMIT ' . self::LIMIT_PARAM_NAME;
        } else {
            $builtLimit = '';
        }

        $sql = trim(<<<SQL
SELECT $builtSelect
FROM $builtFrom
$builtJoins
$builtWhere
$builtGroupBy
$builtOrderBy
$builtLimit
SQL);

        // TODO: Crear una clase que almacene estos datos para hacer esto más OOP
        return [$sql, $this->params];
    }

    protected function generateInPlaceholders(
        string $paramName,
        array $values,
        string $suffix = '_in',
    ): array {
        $placeholders = '';
        $params = [];
        $i = 0;

        foreach ($values as $value) {
            $key = ':' . $paramName . $suffix . $i++;
            $placeholders .= ($placeholders ? ',' : '') . $key; // :id_in0,:id_in1,:id_in2
            $params[$key] = $value; // Agrega el parámetro
        }

        return [$placeholders, $params];
    }

    protected function validateJoinType(string $joinType): void
    {
        if (!in_array($joinType, self::ALLOWED_JOINS)) {
            throw new InvalidArgumentException(
                sprintf(
                    "El parámetro \$joinType debe ser '%s', '%s' pasado",
                    implode("', '", self::ALLOWED_JOINS),
                    $joinType,
                )
            );
        }
    }

    protected function validateOrder(string $order): void
    {
        if (!in_array($order, self::ALLOWED_ORDERS)) {
            throw new InvalidArgumentException(
                sprintf(
                    "El parámetro \$order debe ser '%s', '%s' pasado",
                    implode("', '", self::ALLOWED_ORDERS),
                    $order,
                )
            );
        }
    }
}
