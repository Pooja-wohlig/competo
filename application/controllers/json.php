<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
class Json extends CI_Controller 
{function getallcompetition()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`competo_competition`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";

$elements=array();
$elements[1]=new stdClass();
$elements[1]->field="`competo_competition`.`status`";
$elements[1]->sort="1";
$elements[1]->header="Status";
$elements[1]->alias="status";

$elements=array();
$elements[2]=new stdClass();
$elements[2]->field="`competo_competition`.`name`";
$elements[2]->sort="1";
$elements[2]->header="Name";
$elements[2]->alias="name";

$elements=array();
$elements[3]=new stdClass();
$elements[3]->field="`competo_competition`.`timestamp`";
$elements[3]->sort="1";
$elements[3]->header="Timestamp";
$elements[3]->alias="timestamp";

$elements=array();
$elements[4]=new stdClass();
$elements[4]->field="`competo_competition`.`date`";
$elements[4]->sort="1";
$elements[4]->header="Date";
$elements[4]->alias="date";

$search=$this->input->get_post("search");
$pageno=$this->input->get_post("pageno");
$orderby=$this->input->get_post("orderby");
$orderorder=$this->input->get_post("orderorder");
$maxrow=$this->input->get_post("maxrow");
if($maxrow=="")
{
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `competo_competition`");
$this->load->view("json",$data);
}
public function getsinglecompetition()
{
$id=$this->input->get_post("id");
$data["message"]=$this->competition_model->getsinglecompetition($id);
$this->load->view("json",$data);
}
function getallcompetitionparticipant()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`competo_competitionparticipant`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";

$elements=array();
$elements[1]=new stdClass();
$elements[1]->field="`competo_competitionparticipant`.`name`";
$elements[1]->sort="1";
$elements[1]->header="Name";
$elements[1]->alias="name";

$search=$this->input->get_post("search");
$pageno=$this->input->get_post("pageno");
$orderby=$this->input->get_post("orderby");
$orderorder=$this->input->get_post("orderorder");
$maxrow=$this->input->get_post("maxrow");
if($maxrow=="")
{
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `competo_competitionparticipant`");
$this->load->view("json",$data);
}
public function getsinglecompetitionparticipant()
{
$id=$this->input->get_post("id");
$data["message"]=$this->competitionparticipant_model->getsinglecompetitionparticipant($id);
$this->load->view("json",$data);
}
function getallcompetitionscore()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`competo_competitionscore`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";

$elements=array();
$elements[1]=new stdClass();
$elements[1]->field="`competo_competitionscore`.`user`";
$elements[1]->sort="1";
$elements[1]->header="User";
$elements[1]->alias="user";

$elements=array();
$elements[2]=new stdClass();
$elements[2]->field="`competo_competitionscore`.`competitionparticipant`";
$elements[2]->sort="1";
$elements[2]->header="Competition Participant";
$elements[2]->alias="competitionparticipant";

$elements=array();
$elements[3]=new stdClass();
$elements[3]->field="`competo_competitionscore`.`score`";
$elements[3]->sort="1";
$elements[3]->header="Score";
$elements[3]->alias="score";

$elements=array();
$elements[4]=new stdClass();
$elements[4]->field="`competo_competitionscore`.`comments`";
$elements[4]->sort="1";
$elements[4]->header="comments";
$elements[4]->alias="comments";

$search=$this->input->get_post("search");
$pageno=$this->input->get_post("pageno");
$orderby=$this->input->get_post("orderby");
$orderorder=$this->input->get_post("orderorder");
$maxrow=$this->input->get_post("maxrow");
if($maxrow=="")
{
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `competo_competitionscore`");
$this->load->view("json",$data);
}
public function getsinglecompetitionscore()
{
$id=$this->input->get_post("id");
$data["message"]=$this->competitionscore_model->getsinglecompetitionscore($id);
$this->load->view("json",$data);
}
  public function signIn()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];
        $password = $data['password'];
        if (empty($data)) {
            $data['message'] = 0;
        } else {
            $data['message'] = $this->restapi_model->signIn($email, $password);
        }
        $this->load->view('json', $data);
    }
 public function signUp()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        $contact = $data['contact'];
        if (empty($data)) {
            $data['message'] = 0;
        } else {
            $data['message'] = $this->restapi_model->signUp($name, $email, $password,$contact);
        }
        $this->load->view('json', $data);
    }
} ?>