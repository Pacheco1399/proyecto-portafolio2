<?php

class Post
{
    public int $id_post;
    public int $id_usuario;
    public int $id_comentario;
    public string $title;
    public string $description;
    public string $publication_date;
    public string $modification_date;

    public function __construct(int $id_post, int $id_usuario, int $id_comentario, string $title, string $description, string $publication_date, string $modification_date)
    {
        $this->id_post = $id_post;
        $this->id_usuario = $id_usuario;
        $this->id_comentario = $id_comentario;
        $this->title = $title;
        $this->description = $description;
        $this->publication_date = $publication_date;
        $this->modification_date = $modification_date;

    }
    /*
     *@return int
     */


}