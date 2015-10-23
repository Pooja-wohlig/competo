<?php
if ( !defined( "BASEPATH" ) )
exit( "No direct script access allowed" );
class competitionparticipant_model extends CI_Model
{
public function create($name)
{
$data=array("name" => $name);
$query=$this->db->insert( "competo_competitionparticipant", $data );
$id=$this->db->insert_id();
if(!$query)
return  0;
else
return  $id;
}
public function beforeedit($id)
{
$this->db->where("id",$id);
$query=$this->db->get("competo_competitionparticipant")->row();
return $query;
}
function getsinglecompetitionparticipant($id){
$this->db->where("id",$id);
$query=$this->db->get("competo_competitionparticipant")->row();
return $query;
}
public function edit($id,$name)
{
$data=array("name" => $name);
$this->db->where( "id", $id );
$query=$this->db->update( "competo_competitionparticipant", $data );
return 1;
}
public function delete($id)
{
$query=$this->db->query("DELETE FROM `competo_competitionparticipant` WHERE `id`='$id'");
return $query;
}
}
?>
