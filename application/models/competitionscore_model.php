<?php
if ( !defined( "BASEPATH" ) )
exit( "No direct script access allowed" );
class competitionscore_model extends CI_Model
{
public function create($user,$competitionparticipant,$score,$comments)
{
$data=array("user" => $user,"competitionparticipant" => $competitionparticipant,"score" => $score,"comments" => $comments);
$query=$this->db->insert( "competo_competitionscore", $data );
$id=$this->db->insert_id();
if(!$query)
return  0;
else
return  $id;
}
public function beforeedit($id)
{
$this->db->where("id",$id);
$query=$this->db->get("competo_competitionscore")->row();
return $query;
}
function getsinglecompetitionscore($id){
$this->db->where("id",$id);
$query=$this->db->get("competo_competitionscore")->row();
return $query;
}
public function edit($id,$user,$competitionparticipant,$score,$comments)
{
$data=array("user" => $user,"competitionparticipant" => $competitionparticipant,"score" => $score,"comments" => $comments);
$this->db->where( "id", $id );
$query=$this->db->update( "competo_competitionscore", $data );
return 1;
}
public function delete($id)
{
$query=$this->db->query("DELETE FROM `competo_competitionscore` WHERE `id`='$id'");
return $query;
}
}
?>
