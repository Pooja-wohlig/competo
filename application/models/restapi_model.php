<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class restapi_model extends CI_Model
{
    public function signUp($name, $email, $password, $contact)
    {
        $password = md5($password);
        $query1 = $this->db->query("SELECT `id` FROM `user` WHERE `email`='$email'");
        $num = $query1->num_rows();
        if ($num == 0) {
            $query = $this->db->query('INSERT INTO `user`( `name`, `email`, `password`,`contact`,`logintype`,`accesslevel`,`status`) VALUES ('.$this->db->escape($name).','.$this->db->escape($email).','.$this->db->escape($password).','.$this->db->escape($contact).",'Email','3','1')");
            $id = $this->db->insert_id();
            $newdata = $this->db->query('SELECT  *  FROM `user` WHERE `user`.`id`=('.$this->db->escape($id).')')->row();
            if (!$query) {
                return false;
            } else {
                return $newdata;
            }
        } else {
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
            $newdata = $this->db->query('SELECT  * from `user` WHERE `user`.`id`=('.$this->db->escape($user).')')->row();
            $this->session->set_userdata($newdata);
            //print_r($newdata);
            return $newdata;
        } elseif ($query->num_rows == 0) {
            $query3 = $this->db->query('SELECT `id` FROM `user` WHERE `email`=('.$this->db->escape($email).') AND `forgotpassword`= ('.$this->db->escape($password).')');
            if ($query3->num_rows > 0) {
                $user = $query3->row();
                $user = $user->id;
                $query1 = $this->db->query("UPDATE `user` SET `forgotpassword`='',`password`=(".$this->db->escape($password).') WHERE `email`=('.$this->db->escape($email).')');
                $newdata = $this->db->query('SELECT  * FROM `user` WHERE `user`.`id`=('.$this->db->escape($user).')')->row();

                $this->session->set_userdata($newdata);
                    //print_r($newdata);
                    return $newdata;
            } else {
                return false;
            }
        }
    }
    public function getallcompetitiondetail($competition, $user)
    {
        $compi = new stdClass();
        $query = $this->db->query("SELECT * from `competo_competition` WHERE `id`='$competition'");
        if ($query->num_rows > 0) {
            $compi->detail = $query->row();
            if ($compi->detail->status == '2') {
                $query2 = $this->db->query("SELECT `parti`.`id`,`parti`.`name`,`score` FROM `competo_competitionparticipant` as `parti` LEFT OUTER JOIN `competo_competitionscore` as `score` ON `score`.`competitionparticipant` = `parti`.`id` AND `score`.`user`='$user' WHERE `parti`.`id`='$competition' ");
                if ($query2->num_rows > 0) {
                    $compi->participant = $query2->result();
                } else {
                    $compi->error = 'No Result Inserted';
                }
            } elseif ($compi->detail->status == '3') {
                $query2 = $this->db->query("SELECT `parti`.`id`,`parti`.`name`,AVG(`score`) as `score` FROM `competo_competitionscore` as `score` INNER JOIN `competo_competitionparticipant` as `parti` ON `score`.`competitionparticipant` = `parti`.`id` WHERE `parti`.`id`='$competition' GROUP BY `score`.`competitionparticipant` ");

                if ($query2->num_rows > 0) {
                    $compi->participant = $query2->result();
                } else {
                    $compi->error = 'No Result Inserted';
                }
            }
        } else {
            $compi->error = 'No Such Compitition';
        }

        return $compi;
    }

    public function getParticipantDetails($participant)
    {
        $compi = new stdClass();
        $query = $this->db->query("SELECT `competo_competitionscore`.`id`,`competo_competitionscore`.`score`,`competo_competitionscore`.`comments`,`user`.`name` from `competo_competitionscore` INNER JOIN `user` ON `competo_competitionscore`.`user`=`user`.`id` WHERE `competitionparticipant`='$participant'");
        if ($query->num_rows > 0) {
            $compi = $query->result();
        } else {
            $compi->error = 'No Such Compitition';
        }

        return $compi;
    }

    public function postScore($participant,$score,$comment,$user)
    {
        $this->db->query("SELECT * FROM `competo_competitionscore` WHERE `user`='$user' AND `competitionparticipant` = '$participant'");
        if($query->num_rows > 0) {
          $id=$query->id;
          $this->db>query("UPDATE `competo_competitionscore` SET `comments` = '$comment' , `score` = '$score' WHERE `competo_competitionscore`.`id` = '$id'");
          return $id;
        }
        else {
          $this->db->query("INSERT INTO `competo_competitionscore` (`id`, `user`, `competitionparticipant`, `score`, `comments`) VALUES (null, '$user', '$participant', '$score', '$comment');");
        }
        return $this->db->insert_id();
    }

}
