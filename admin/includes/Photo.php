<?php

class Photo extends Db_object
{
    protected static $table_name = 'photos';
    public $id;
    public $title;
    public $description;
    public $filename;
    public $size;
    public $type;
    public $alternate_text;
    public $user_id;
    public $created_at;
    public $deleted_at;

    public $tmp_path;
    public $upload_directory = "assets/images/photos";
    public $errors = array(); //of []
    public $upload_errors_array = [
        UPLOAD_ERR_OK => "There is no error",
        UPLOAD_ERR_INI_SIZE=>"The uploaded file exceeds the upload max_filesize from php.ini",
        UPLOAD_ERR_FORM_SIZE=>"The uploaded file exceeds MAX_FILE_SIZE in php.ini voor een html form",
        UPLOAD_ERR_NO_FILE=>"No file uploaded",
        UPLOAD_ERR_PARTIAL => "The file was partially uploaded",
        UPLOAD_ERR_NO_TMP_DIR=>"Missing temp folder",
        UPLOAD_ERR_CANT_WRITE=>"Failed to write to disk",
        UPLOAD_ERR_EXTENSION=>"A php extension stopped your upload",
    ];

    // Maximum file size in bytes (5MB)
    const MAX_FILE_SIZE = 5242880;
    // Allowed file types
    const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    /**
     * Retrieves the properties of the Photo object as an associative array.
     *
     * @return array An associative array containing the properties of the Photo object:
     *               - 'id': The ID of the photo.
     *               - 'title': The title of the photo.
     *               - 'description': The description of the photo.
     *               - 'filename': The filename of the photo.
     *               - 'size': The size of the photo.
     *               - 'type': The type of the photo.
     */
    public function get_properties(){
        return[
            'id'=> $this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'filename'=>$this->filename,
            'size'=>$this->size,
            'type'=>$this->type,
            'alternate_text'=>$this->alternate_text,
            'user_id' => $this->user_id,
        ];
    }
    public function set_file($file){
        if(empty($file) || !$file || !is_array($file)){
            $this->errors[]="No file uploaded";
            return false;
        }elseif($file['error'] != 0){
            $this->errors[]= $this->upload_errors_array['error'];
            return false;
        }elseif($file['size'] > self::MAX_FILE_SIZE){
            $this->errors[] = "File size exceeds maximum limit of 5MB";
            return false;
        }elseif(!in_array($file['type'], self::ALLOWED_TYPES)){
            $this->errors[] = "Invalid file type. Only JPEG, PNG and GIF files are allowed";
            return false;
        }else{
            $date = date('Y_m_d_H_i_s');
            $without_extension  = pathinfo(basename($file['name']), PATHINFO_FILENAME);
            $extension = pathinfo(basename($file['name']), PATHINFO_EXTENSION);
            $this->filename = $without_extension.$date.'.'.$extension;
            $this->type = $file['type'];
            $this->size = $file['size'];
            $this->tmp_path= $file['tmp_name'];
            return true;
        }
    }
    public function save() {
        $target_path = SITE_ROOT . DS . 'admin' . DS . $this->upload_directory . DS . $this->filename;

        // Als het Photo-object al een ID heeft, wordt dit beschouwd als een update.
        if ($this->id) {
            // Controleer of er een tijdelijk bestandspad aanwezig is (nieuw bestand).
            if (!empty($this->tmp_path)) {
                // Het nieuwe bestand wordt verplaatst naar de juiste locatie en opgeslagen.
                if (move_uploaded_file($this->tmp_path, $target_path)) {
                    $this->update(); // Update database
                    unset($this->tmp_path);// Het tijdelijke pad wordt verwijderd.
                    return true;
                } else {
                    $this->errors[] = "Failed to move the file.";
                    return false;
                }
            } else {
                //update met een leeg bestand, dus geen bestand meegegeven
                return $this->update(); // Alleen database bijwerken
            }
        } else { // Nieuw bestand uploaden
            if (!empty($this->errors)) {
                return false;
            }
            if (empty($this->filename) || empty($this->tmp_path)) {
                $this->errors[] = "The file is not available.";
                return false;
            }
            if (file_exists($target_path)) {
                $this->errors[] = "The file {$this->filename} already exists.";
                return false;
            }
            if (move_uploaded_file($this->tmp_path, $target_path)) {
                if ($this->create()) { // Database-insert
                    global$database;
                    $this->id = $database->get_last_insert_id(); // Gebruik de niet-statische methode
                    unset($this->tmp_path);
                    return true;
                }
            } else {
                $this->errors[] = "This folder does not have write permissions.";
                return false;
            }
        }
    }

    public function picture_path() {
        $file_path = SITE_ROOT . DS . 'admin' . DS . $this->upload_directory . DS . $this->filename;

        if ($this->filename && file_exists($file_path)) {
            return $this->upload_directory . DS . $this->filename;
        }

        return 'https://placehold.co/300';
    }
    // Deze methode verwijdert de oude afbeelding fysiek van de server.
    // Dit gebeurt alleen als er een bestand is gekoppeld aan het Photo-object.
    public function update_photo() {
        if (!empty($this->filename)) {
            $target_path = SITE_ROOT . DS . 'admin' . DS . $this->upload_directory . DS . $this->filename;
            if (file_exists($target_path)) {
                unlink($target_path); // Verwijder de oude afbeelding fysiek
            }
        }
    }

    public function associate_with_blog($blog_id) {
        global $database;

        if (empty($this->id) || empty($blog_id)) {
            $this->errors[] = "Missing photo ID or blog ID";
            return false;
        }

        $sql = "INSERT INTO blogs_photos (blog_id, photo_id) VALUES (?, ?)";
        return $database->query($sql, [$blog_id, $this->id]);
    }
    public function delete() {
        // First remove the file physically
        if (!empty($this->filename)) {
            $target_path = SITE_ROOT . DS . 'admin' . DS . $this->upload_directory . DS . $this->filename;
            if (file_exists($target_path)) {
                unlink($target_path);
            }
        }

        // Remove blog associations
        global $database;
        $sql = "DELETE FROM blogs_photos WHERE photo_id = ?";
        $database->query($sql, [$this->id]);

        // Then delete the database record
        return parent::delete();
    }
}
?>