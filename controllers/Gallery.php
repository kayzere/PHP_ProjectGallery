<?php
class Gallery extends Controller {
  public function index() {
    $this->albums();
  }
  
  public function albums() {
      //var_dump($this->gallery->albums());
      $this->loader->load('albums', ['title'=>'Albums', 'albums'=>$this->gallery->albums()]);
  }

  public function albums_new() {
    if ($this->redirect_unlogged_user()) return;
    $this->loader->load('albums_new', ['title'=>'Création d\'un album']);
  }

  public function albums_create() {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_name = filter_input(INPUT_POST, 'album_name');
      $this->gallery->create_album($album_name);
      header('Location: /index.php/gallery/albums'); /* redirection du client vers la liste des albums. */
    } catch (Exception $e) {
      $this->loader->load('albums_new',
                      ['title'=>'Création d\'un album',
                       'error_message' => $e->getMessage()]);
    }
  }
  
  public function albums_delete($album_id) {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_id = filter_var($album_id);
      $this->gallery->delete_album($album_id);
    } catch (Exception $e) { }
    header('Location: /index.php/gallery/albums');
  }

  public function albums_show($album_id) {
    try {
        //var_dump($this->gallery->photos($album_name));
        $albums_id = filter_var($album_id);
        $this->gallery->check_if_album_exists($album_id);
        $album_name = $this->gallery->album_name($album_id);
        $this->gallery->photos($album_id);
        $this->loader->load('albums_show',
                          ['title'=>$album_name,
                           'album'=>$album_id,
                           'photos'=>$this->gallery->photos($album_id)]);
    } catch (Exception $e) {
      header("Location: /index.php");
    }
  }
  
  public function photos_new($album_id) {
    if ($this->redirect_unlogged_user()) return;
    try{  
      $albums_id = filter_var($album_id);
      $this->gallery->check_if_album_exists($album_id);   
      $album_name = $this->gallery->album_name($album_id);
      $title="Création d'une photo $album_name";
      $this->loader->load('photos_new', ['title'=>$title,
                                        'album'=>$album_id,
                                        'album_name'=>$album_name,
                                        ]);
    } 
    catch (Exeption $e) {
      Header("Location: /index.php/gallery/albums");
    }
  }


  public function photos_add($album_id) {
    if ($this->redirect_unlogged_user()) return;
    try {
          $album_id = filter_var($album_id);
          $this->gallery->check_if_album_exists($album_id);
          $album_name = $this->gallery->album_name($album_id);
       } catch (Exception $e) {
          header("Location: /index.php"); 
    }
    try {
       $photo_name = filter_input(INPUT_POST,'photo_name');
       if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
           throw new Exception('Vous devez choisir une photo.');
       }
       $this->gallery->add_photo($album_id, $photo_name, $_FILES['photo']['tmp_name']);
       header("Location: /index.php/gallery/albums_show/$album_id");
    } catch (Exception $e) {
          $this->loader->load('photos_new', ['album_name'=>$album_name,
                                            'album'=>$album_id,
                                            'title'=>"Ajout d'une photo dans l'album $album_name",
                                            'error_message' => $e->getMessage()]);
    }
  }
  
  public function photos_delete($album_id, $photo_id) {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_id = filter_var($album_id);
      $photo_id = filter_var($photo_id);
      $this->gallery->delete_photo($photo_id);
      $this->gallery->check_if_album_exists($album_id);
      $this->albums_show($album_id);
      header("Location: /index.php/gallery/albums_show/$album_id"); 
    } catch (Exception $e) {
      header('Location: /index.php/gallery');  
    }
  }
  
  public function photos_show($album_id,$photo_id) {
    try {
      $this->gallery->check_if_album_exists($album_id);
      $album_id = filter_var($album_id);
      $photo_id = filter_var($photo_id);
      $album_name = $this->gallery->album_name($album_id);
      $photo_name = $this->gallery->photo_name($album_id);

      $title = "$album_name/$photo_name";
      $this->loader->load('photos_show', ['title'=>$title,
                                          'album'=>$album_id,
                                          'photo'=>$this->gallery->photo($album_id,$photo_id)
                                          ]);
      } catch (Exception $e) {
    var_dump($e->getMessage());
    header("Location: /index.php");
    }
  }
  public function photos_get($photo_id) {
    try {
      $photo_id = filter_var($photo_id);
      if (isset($_GET['thumbnail'])) { $data = $this->gallery->thumbnail($photo_id); }
      else { $data =  $this->gallery->fullsize($photo_id); }
      header("Content-Type: image/jpeg"); // modification du header pour changer le format des données retourné au client
      echo $data;                          // écriture du binaire de l'image vers le client
    } catch (Exception $e) { }
  }

  private function redirect_unlogged_user() {
    if (!$this->sessions->user_is_logged()) {
      header('Location: /index.php/sessions/sessions_new');
      return true;
    }
    return false;
  }
}