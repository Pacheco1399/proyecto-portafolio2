<?php

namespace App\Core;

use App\Core\Database\SelectQueryBuilder;
use App\Utils\DatabaseUtils;
use PDOStatement;

/**
 * Sacado desde Pulse Pricing
 *
 * Repositorio con métodos útiles (find*).
 *
 * TODO: A futuro acá se podrá utilizar genéricos
 */
abstract class AbstractRepository


{
    public function __construct(
        protected DatabaseConnection $db,
        protected DatabaseUtils $dbUtils,
    ) {
    }
    /**
     * Obtiene una o varias filas de datos a partir de los filtros pasados.
     * Se puede configurar la ejecución a partir de las opciones pasadas, como
     * por ejemplo paginando los datos, obteniendo sólo el primero, etc.
     *
     * @param array $filters Filtros a utilizar al obtener datos
     * @param array $options Opciones para configurar la ejecución
     * @return mixed Los datos, según los filtros y las opciones pasadas
     */
    abstract function findBy(array $filters, array $options);

    /**
     * Obtiene todas las filas, permitiendo configurar la ejecución a partir de
     * las opciones pasadas.
     *
     * @param array $options Opciones para configurar la ejecución
     * @return mixed Los datos, según las opciones pasadas
     */
    public function findAll(array $options = [])
    {
        return $this->findBy([], $options);
    }

    /**
     * Obtiene todas las filas excepto la que tenga la ID pasada.
     *
     * Permite filtrar los resultados y configurar la ejecución a partir de las
     * opciones pasadas.
     *
     * @param array $filters Filtros a utilizar al obtener datos
     * @param array $options Opciones para configurar la ejecución
     * @return mixed Los datos, según las opciones pasadas
     */
    public function findAllExcept(int $id, array $filters = [], array $options = [])
    {
        return $this->findBy(['id_not' => $id] + $filters, $options);
    }

    /**
     * Obtiene la primera fila que cumpla con los filtros pasados.
     *
     * Se puede configurar la ejecución a partir de las opciones pasadas, como
     * por ejemplo paginando los datos, obteniendo sólo el primero, etc.
     *
     * @param array $filters Filtros a utilizar al obtener datos
     * @param array $options Opciones para configurar la ejecución
     * @return mixed Los datos, según los filtros y las opciones pasadas
     */
    public function findOneBy(array $filters, array $options = [])
    {
        return $this->findBy($filters, [
            'limit' => 1,
            'fetch_first_only' => true,
        ] + $options);
    }




    /**
     * Obtiene una fila por la ID, opcionalmente permitiendo filtros adicionales.
     */
    public function find(int $id, array $filters = [], array $options = [])
    {
        return $this->findOneBy(['id' => $id] + $filters, $options);
    }

    protected function createSelectQueryBuilder(
        string $tableName,
        string $alias,
        ?array $filters = null,
        ?array $options = null
    ): SelectQueryBuilder {
        $qb = new SelectQueryBuilder($tableName, $alias);

        // Configura la query
        if ($options) {
            if (isset($options['select'])) {
                if (is_array($options['select'])) {
                    $qb->select(...$options['select']);
                } else {
                    $qb->select($options['select']);
                }
            }

            if (isset($options['extra_select'])) {
                $qb->addSelect($options['extra_select']);
            }

            if (isset($options['order_by']) && is_array($options['order_by'])) {
                /*// Remueve los órdenes anteriores
                $qb->orderBy();*/

                foreach ($options['order_by'] as $criteria => $order) {
                    $qb->addOrderBy($criteria, $order);
                }
            }

            if (isset($options['group_by'])) {
                if (is_array($options['group_by'])) {
                    $qb->groupBy(...$options['group_by']);
                } else {
                    $qb->groupBy($options['group_by']);
                }
            }

            if (isset($options['limit'])) {
                $qb->limit($options['limit']);
            }

            if (isset($options['offset'])) {
                $qb->offset($options['offset']);
            }
        }

        // Agrega filtros básicos
        if ($filters) {
            if (isset($filters['id'])) {
                $qb->andWhereEqual("$alias.id", $filters['id']);
            }

            if (isset($filters['id_not'])) {
                $qb->andWhereNotEqual("$alias.id", $filters['id_not']);
            }

            //if (!empty($filters['id_in'])) {
            if (isset($filters['id_in'])) {
                $qb->andWhereIn("$alias.id", $filters['id_in']);
            }

            //if (!empty($filters['id_not_in'])) {
            if (isset($filters['id_not_in'])) {
                $qb->andWhereNotIn("$alias.id", $filters['id_not_in']);
            }

            if (isset($filters['id_greater_than'])) {
                $qb->andWhere("$alias.id > :id_greater_than", [
                    ':id_greater_than' => $filters['id_greater_than'],
                ]);
            }

            if (isset($filters['id_greater_than_or_equal'])) {
                $qb->andWhere("$alias.id >= :id_greater_than_or_equal", [
                    ':id_greater_than_or_equal' => $filters['id_greater_than_or_equal'],
                ]);
            }

            if (isset($filters['id_lower_than'])) {
                $qb->andWhere("$alias.id < :id_lower_than", [
                    ':id_lower_than' => $filters['id_lower_than'],
                ]);
            }

            if (isset($filters['id_lower_than_or_equal'])) {
                $qb->andWhere("$alias.id <= :id_lower_than_or_equal", [
                    ':id_lower_than_or_equal' => $filters['id_lower_than_or_equal'],
                ]);
            }
        }

        return $qb;
    }

    protected function fetchFromStatement(
        PDOStatement $stmt,
        array $options = [],
    ) {
        // Si se define, carga sólo la primera fila, ahorrando memoria en el proceso
        if (isset($options['fetch_first_only']) && $options['fetch_first_only']) {
            $fetchedValue = $stmt->fetch();

            // Cuando no hay ninguna fila, PDO retorna false en vez de null, lo
            // cual puede no ser esperado: con esto se corrige eso
            if (
                $fetchedValue === false
                // Se habilita la opción de retornar false en estos casos
                && (!isset($options['allow_return_false']) || !$options['allow_return_false'])
            ) {
                return null;
            }

            // Carga el valor obtenido
            return $fetchedValue;
        }

        // Retorna todas las filas obtenidas
        return $stmt->fetchAll();
    }
}
