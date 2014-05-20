<?php
/**
 * Access from index.php:
 */

class Visitante_Controller extends ZP_Load {
  
  public function __construct() {
    $this->app("visitante");

    $this->helper('debugging');

    $this->Templates = $this->core("Templates");

    $this->Templates->theme();

    $this->Visitante_Model = $this->model("Visitante_Model");

    $this->TipoDoc_Model = $this->model("TipoDoc_Model");
    $this->Rol_Model = $this->model("Rol_Model");
  }

  public function index() {
    $this->lista();
  }
  
  public function lista() {
    $this->helper('tabla');
    $vars["headers"] = array('Documento', 'Tipo documento', 'Nombres', 'Apellido', 'Rol', 'Teléfono');
    $datos = $this->Visitante_Model->getTableData();
    $vars["data"] = $datos;
    
    $vars["view"] = $this->view("tabla_datos", TRUE);

    $this->render("content", $vars);
  }
  
  public function agregar() {
    $this->helper(array('forms', 'html'));

    $vars["tipo_doc"] = $this->TipoDoc_Model->getDataForSelect('CC');

    $vars["roles"] = $this->Rol_Model->getDataForSelect(1);

    $vars["view"]  = $this->view("editar", TRUE);
    
    $this->render("content", $vars);
  }

  public function guardar() {
    if (POST('guardar')) {
      //guardar registro
      $this->Visitante_Model->save();
    } elseif (POST('cancel')) {
      redirect('visitante');
    } else {
      redirect('visitante/agregar');
    }
    // showAlert("El registro se guardó satisfactoriamente.", 'index');
  }

  public function editar($cc = NULL) {
    if (empty($cc)) redirect('visitante/lista');

    $this->helper(array('forms', 'html'));
    $datos = $this->Visitante_Model->getByCC($cc);
    
    $vars["datos"] = $datos;
    $vars['editar'] = TRUE;

    $vars["tipo_doc"] = $this->TipoDoc_Model->getDataForSelect($datos['tipo_doc']);

    $vars["roles"] = $this->Rol_Model->getDataForSelect($datos['tipo_user']);

    $vars["view"]  = $this->view("editar", TRUE);
    
    $this->render("content", $vars);
  }

  public function eliminar($cc) {
    $this->helper(array('forms', 'html'));
    $datos = $this->Visitante_Model->getByCC($cc);

    if (POST('eliminar')) {
      $this->Visitante_Model->delete($cc);
    } elseif (POST('cancel')) {
      redirect("visitante");
    } else {
      $vars['action'] = "eliminar a {$datos['nombres']} {$datos['apellido1']} {$datos['apellido2']} ({$datos['identificacion']})";
      $vars['url'] = 'visitante/eliminar/' . $cc;
      $vars["view"]  = $this->view("confirmacion", TRUE);
      
      $this->render("content", $vars);
    }
  }

  public function buscar($text) {
    $text = str_replace('-', ' ', $text);
    $datos = $this->Visitante_Model->search($text);
    $this->helper('tabla');
    $vars["headers"] = array('Documento', 'Tipo documento', 'Nombres', 'Apellido 1', 'Apellido 2', 'Rol');
    $vars["data"] = $datos;
    
    $vars["view"] = $this->view("resultados", TRUE);

    $this->render("content", $vars);
  }
}
?>