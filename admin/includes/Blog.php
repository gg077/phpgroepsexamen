<?php

class Blog extends Db_object
{
    public $id;
    public $author_id;
    public $photo_id;
    public $title;
    public $description;
    public $created_at;
    public $deleted_at;
    protected static $table_name = 'blogs';

    public function get_properties(){
        return[
            'id'=> $this->id,
            'author_id'=>$this->author_id,
            'photo_id'=>$this->photo_id,
            'title'=>$this->title,
            'description'=>$this->description,
            'created_at'=>$this->created_at,
            'deleted_at'=>$this->deleted_at
        ];
    }
    /**
     * Slaat de geselecteerde categorieën op voor deze blogpost.
     *
     * Eerst wordt de tabel `blogs_categories` leeggemaakt voor deze blogpost,
     * en daarna worden de geselecteerde categorieën toegevoegd.
     *
     * @param array $category_ids De IDs van de categorieën die gekoppeld moeten worden aan deze blogpost.
     * @return void
     */
    public function save_categories($category_ids) {
        global $database;

        // Verwijder eerst bestaande categorieën voor deze blogpost
        $sql = "DELETE FROM blogs_categories WHERE blog_id = ?";
        $database->query($sql, [$this->id]);

        // Voeg de geselecteerde categorieën toe
        if (!empty($category_ids)) {
            foreach ($category_ids as $category_id) {
                $sql = "INSERT INTO blogs_categories (blog_id, category_id) VALUES (?, ?)";
                $database->query($sql, [$this->id, $category_id]);
            }
        }
    }

    /**
     * Returns an array of categories for a given blog post.
     *
     * @param int $blog_id The ID of the blog post for which to retrieve the categories.
     * @return array An array of category objects, or an empty array if no categories are found.
     */
    public static function get_categories($blog_id) {
        global $database;

        // Controleer of het blog ID geldig is
        if (empty($blog_id)) {
            return [];
        }

        // Query om categorieën op te halen voor een specifieke blogpost
        $sql = "SELECT c.* FROM categories c 
            INNER JOIN blogs_categories bc ON c.id = bc.category_id 
            WHERE bc.blog_id = ?";

        $result = $database->query($sql, [$blog_id]);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


}









