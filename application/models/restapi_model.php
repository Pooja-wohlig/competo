<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class restapi_model extends CI_Model
{
   
    public function signUp($name, $email, $password,$contact)
    {
        $password = md5($password);
         $query1=$this->db->query("SELECT `id` FROM `user` WHERE `email`='$email'");
				$num=$query1->num_rows();
        if($num == 0)
        {
            $query = $this->db->query('INSERT INTO `user`( `name`, `email`, `password`,`contact`,`logintype`,`accesslevel`,`status`) VALUES ('.$this->db->escape($name).','.$this->db->escape($email).','.$this->db->escape($password).','.$this->db->escape($contact).",'Email','3','1')");
            $id = $this->db->insert_id();
          $newdata = $this->db->query('SELECT  `user`.`id`, `user`.`name`, `user`.`email`, `user`.`accesslevel`, `user`.`timestamp`, `user`.`status`, `user`.`image`, `user`.`username`, `user`.`socialid`, `user`.`logintype`, `user`.`address`, `user`.`contact`, `user`.`dob`, `user`.`street`, `user`.`city`, `user`.`state`, `user`.`country`, `user`.`pincode`, `user`.`facebook`, `user`.`google`, `user`.`twitter`, `user`.`website`, `user`.`forgotpassword`, `user`.`coverimage`, `user`.`about`, `user`.`hobbies`, `user`.`profession`,`userimages`.`image` FROM `user` LEFT OUTER JOIN `userimages` ON `userimages`.`user`=`user`.`id` WHERE `user`.`id`=('.$this->db->escape($id).')')->row();
            if (!$query) {
                return false;
            } else {
                return $newdata;
            }
       
    }
        else{
        return false;
        }
    }
    public function signIn($email, $password)
    {
        $password = md5($password);
        $query = $this->db->query('SELECT `id` FROM `user` WHERE `email`=('.$this->db->escape($email).') AND `password`= ('.$this->db->escape($password).')');
        if ($query->num_rows > 0) {
            $user = $query->row();
            $user = $user->id;
            $query1 = $this->db->query("UPDATE `user` SET `forgotpassword`='' WHERE `email`=(".$this->db->escape($email).')');
           $newdata = $this->db->query('SELECT  `user`.`id`, `user`.`name`, `user`.`email`, `user`.`accesslevel`, `user`.`timestamp`, `user`.`status`, `user`.`image`, `user`.`username`, `user`.`socialid`, `user`.`logintype`, `user`.`address`, `user`.`contact`, `user`.`dob`, `user`.`street`, `user`.`city`, `user`.`state`, `user`.`country`, `user`.`pincode`, `user`.`facebook`, `user`.`google`, `user`.`twitter`, `user`.`website`, `user`.`forgotpassword`, `user`.`coverimage`, `user`.`about`, `user`.`hobbies`, `user`.`profession`,`userimages`.`image` FROM `user` LEFT OUTER JOIN `userimages` ON `userimages`.`user`=`user`.`id` WHERE `user`.`id`=('.$this->db->escape($user).')')->row();
            $this->session->set_userdata($newdata);
            //print_r($newdata);
            return $newdata;
        } elseif ($query->num_rows == 0) {
            $query3 = $this->db->query('SELECT `id` FROM `user` WHERE `email`=('.$this->db->escape($email).') AND `forgotpassword`= ('.$this->db->escape($password).')');
            if ($query3->num_rows > 0) {
                $user = $query3->row();
                $user = $user->id;
                $query1 = $this->db->query("UPDATE `user` SET `forgotpassword`='',`password`=(".$this->db->escape($password).') WHERE `email`=('.$this->db->escape($email).')');
                $newdata = $this->db->query('SELECT  `user`.`id`, `user`.`name`, `user`.`email`, `user`.`accesslevel`, `user`.`timestamp`, `user`.`status`, `user`.`image`, `user`.`username`, `user`.`socialid`, `user`.`logintype`, `user`.`address`, `user`.`contact`, `user`.`dob`, `user`.`street`, `user`.`city`, `user`.`state`, `user`.`country`, `user`.`pincode`, `user`.`facebook`, `user`.`google`, `user`.`twitter`, `user`.`website`, `user`.`forgotpassword`, `user`.`coverimage`, `user`.`about`, `user`.`hobbies`, `user`.`profession`,`userimages`.`image` FROM `user` LEFT OUTER JOIN `userimages` ON `userimages`.`user`=`user`.`id` WHERE `user`.`id`=('.$this->db->escape($user).')')->row();

                $this->session->set_userdata($newdata);
                    //print_r($newdata);
                    return $newdata;
            } else {
                return false;
            }
        }
    }
  
  
}
