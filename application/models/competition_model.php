<?php
if ( !defined( "BASEPATH" ) )
exit( "No direct script access allowed" );
class competition_model extends CI_Model
{
public function create($status,$name,$timestamp,$date)
{
$data=array("status" => $status,"name" => $name,"timestamp" => $timestamp,"date" => $date);
$query=$this->db->insert( "competo_competition", $data );
$id=$this->db->insert_id();
if(!$query)
return  0;
else
return  $id;
}
public function beforeedit($id)
{
$this->db->where("id",$id);
$query=$this->db->get("competo_competition")->row();
return $query;
}
function getsinglecompetition($id){
$this->db->where("id",$id);
$query=$this->db->get("competo_competition")->row();
return $query;
}
public function edit($id,$status,$name,$timestamp,$date)
{
$data=array("status" => $status,"name" => $name,"timestamp" => $timestamp,"date" => $date);
$this->db->where( "id", $id );
$query=$this->db->update( "competo_competition", $data );
return 1;
}
public function delete($id)
{
$query=$this->db->query("DELETE FROM `competo_competition` WHERE `id`='$id'");
return $query;
}
}
?>
