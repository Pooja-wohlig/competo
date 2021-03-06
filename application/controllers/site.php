<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Site extends CI_Controller
{
	public function __construct( )
	{
		parent::__construct();

		$this->is_logged_in();
	}
	function is_logged_in( )
	{
		$is_logged_in = $this->session->userdata( 'logged_in' );
		if ( $is_logged_in !== 'true' || !isset( $is_logged_in ) ) {
			redirect( base_url() . 'index.php/login', 'refresh' );
		} //$is_logged_in !== 'true' || !isset( $is_logged_in )
	}
	function checkaccess($access)
	{
		$accesslevel=$this->session->userdata('accesslevel');
		if(!in_array($accesslevel,$access))
			redirect( base_url() . 'index.php/login/logout/?alerterror=You do not have access to this page. ', 'refresh' );
	}
	public function index()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$data[ 'page' ] = 'dashboard';
		$data[ 'title' ] = 'Welcome';
		$this->load->view( 'template', $data );
	}
	public function createuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
//        $data['category']=$this->category_model->getcategorydropdown();
		$data[ 'page' ] = 'createuser';
		$data[ 'title' ] = 'Create User';
		$this->load->view( 'template', $data );
	}
	function createusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[user.email]');
		$this->form_validation->set_rules('password','Password','trim|required|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|required|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('socialid','Socialid','trim');
		$this->form_validation->set_rules('logintype','logintype','trim');
		$this->form_validation->set_rules('json','json','trim');
		if($this->form_validation->run() == FALSE)
		{
			$data['alerterror'] = validation_errors();
			$data['accesslevel']=$this->user_model->getaccesslevels();
            $data[ 'status' ] =$this->user_model->getstatusdropdown();
            $data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
            $data[ 'page' ] = 'createuser';
            $data[ 'title' ] = 'Create User';
            $this->load->view( 'template', $data );
		}
		else
		{
            $name=$this->input->post('name');
            $email=$this->input->post('email');
            $password=$this->input->post('password');
            $accesslevel=$this->input->post('accesslevel');
            $status=$this->input->post('status');
            $socialid=$this->input->post('socialid');
            $logintype=$this->input->post('logintype');
            $json=$this->input->post('json');
//            $category=$this->input->post('category');

            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];

                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r);
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                    //return false;
                }
                else
                {
                    //print_r($this->image_lib->dest_image);
                    //dest_image
                    $image=$this->image_lib->dest_image;
                    //return false;
                }

			}

			if($this->user_model->create($name,$email,$password,$accesslevel,$status,$socialid,$logintype,$image,$json)==0)
			$data['alerterror']="New user could not be created.";
			else
			$data['alertsuccess']="User created Successfully.";
			$data['redirect']="site/viewusers";
			$this->load->view("redirect",$data);
		}
	}
    function viewusers()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewusers';
        $data['base_url'] = site_url("site/viewusersjson");

		$data['title']='View Users';
		$this->load->view('template',$data);
	}
    function viewusersjson()
	{
		$access = array("1");
		$this->checkaccess($access);


        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`user`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";


        $elements[1]=new stdClass();
        $elements[1]->field="`user`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";

        $elements[2]=new stdClass();
        $elements[2]->field="`user`.`email`";
        $elements[2]->sort="1";
        $elements[2]->header="Email";
        $elements[2]->alias="email";

        $elements[3]=new stdClass();
        $elements[3]->field="`user`.`socialid`";
        $elements[3]->sort="1";
        $elements[3]->header="SocialId";
        $elements[3]->alias="socialid";

        $elements[4]=new stdClass();
        $elements[4]->field="`logintype`.`name`";
        $elements[4]->sort="1";
        $elements[4]->header="Logintype";
        $elements[4]->alias="logintype";

        $elements[5]=new stdClass();
        $elements[5]->field="`user`.`json`";
        $elements[5]->sort="1";
        $elements[5]->header="Json";
        $elements[5]->alias="json";

        $elements[6]=new stdClass();
        $elements[6]->field="`accesslevel`.`name`";
        $elements[6]->sort="1";
        $elements[6]->header="Accesslevel";
        $elements[6]->alias="accesslevelname";

        $elements[7]=new stdClass();
        $elements[7]->field="`statuses`.`name`";
        $elements[7]->sort="1";
        $elements[7]->header="Status";
        $elements[7]->alias="status";


        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow=20;
        }

        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }

        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `user` LEFT OUTER JOIN `logintype` ON `logintype`.`id`=`user`.`logintype` LEFT OUTER JOIN `accesslevel` ON `accesslevel`.`id`=`user`.`accesslevel` LEFT OUTER JOIN `statuses` ON `statuses`.`id`=`user`.`status`");

		$this->load->view("json",$data);
	}


	function edituser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
		$data['before']=$this->user_model->beforeedit($this->input->get('id'));
		$data['page']='edituser';
//		$data['page2']='block/userblock';
		$data['title']='Edit User';
		$this->load->view('template',$data);
	}
	function editusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);

		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('password','Password','trim|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('socialid','Socialid','trim');
		$this->form_validation->set_rules('logintype','logintype','trim');
		$this->form_validation->set_rules('json','json','trim');
		if($this->form_validation->run() == FALSE)
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->user_model->getstatusdropdown();
			$data['accesslevel']=$this->user_model->getaccesslevels();
            $data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
			$data['before']=$this->user_model->beforeedit($this->input->post('id'));
			$data['page']='edituser';
//			$data['page2']='block/userblock';
			$data['title']='Edit User';
			$this->load->view('template',$data);
		}
		else
		{

            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
            $email=$this->input->get_post('email');
            $password=$this->input->get_post('password');
            $accesslevel=$this->input->get_post('accesslevel');
            $status=$this->input->get_post('status');
            $socialid=$this->input->get_post('socialid');
            $logintype=$this->input->get_post('logintype');
            $json=$this->input->get_post('json');
//            $category=$this->input->get_post('category');

            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];

                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r);
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                    //return false;
                }
                else
                {
                    //print_r($this->image_lib->dest_image);
                    //dest_image
                    $image=$this->image_lib->dest_image;
                    //return false;
                }

			}

            if($image=="")
            {
            $image=$this->user_model->getuserimagebyid($id);
               // print_r($image);
                $image=$image->image;
            }

			if($this->user_model->edit($id,$name,$email,$password,$accesslevel,$status,$socialid,$logintype,$image,$json)==0)
			$data['alerterror']="User Editing was unsuccesful";
			else
			$data['alertsuccess']="User edited Successfully.";

			$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);

		}
	}

	function deleteuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->deleteuser($this->input->get('id'));
//		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="User Deleted Successfully";
		$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
		$this->load->view("redirect",$data);
	}
	function changeuserstatus()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->changestatus($this->input->get('id'));
		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="Status Changed Successfully";
		$data['redirect']="site/viewusers";
        $data['other']="template=$template";
        $this->load->view("redirect",$data);
	}



    public function viewcompetition()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="viewcompetition";
$data["base_url"]=site_url("site/viewcompetitionjson");
$data["title"]="View competition";
$this->load->view("template",$data);
}
function viewcompetitionjson()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`competo_competition`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";
$elements[1]=new stdClass();
$elements[1]->field="`competo_competition`.`status`";
$elements[1]->sort="1";
$elements[1]->header="Status";
$elements[1]->alias="status";
$elements[2]=new stdClass();
$elements[2]->field="`competo_competition`.`name`";
$elements[2]->sort="1";
$elements[2]->header="Name";
$elements[2]->alias="name";
$elements[3]=new stdClass();
$elements[3]->field="`competo_competition`.`timestamp`";
$elements[3]->sort="1";
$elements[3]->header="Timestamp";
$elements[3]->alias="timestamp";
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
$maxrow=20;
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `competo_competition`");
$this->load->view("json",$data);
}

public function createcompetition()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="createcompetition";
$data[ 'status' ] =$this->user_model->getstatusdropdown();
$data["title"]="Create competition";
$this->load->view("template",$data);
}
public function createcompetitionsubmit()
{
$access=array("1");
$this->checkaccess($access);
$this->form_validation->set_rules("status","Status","trim");
$this->form_validation->set_rules("name","Name","trim");
$this->form_validation->set_rules("timestamp","Timestamp","trim");
$this->form_validation->set_rules("date","Date","trim");
if($this->form_validation->run()==FALSE)
{
$data["alerterror"]=validation_errors();
$data["page"]="createcompetition";
$data[ 'status' ] =$this->user_model->getstatusdropdown();
$data["title"]="Create competition";
$this->load->view("template",$data);
}
else
{
$status=$this->input->get_post("status");
$name=$this->input->get_post("name");
$timestamp=$this->input->get_post("timestamp");
$date=$this->input->get_post("date");
if($this->competition_model->create($status,$name,$timestamp,$date)==0)
$data["alerterror"]="New competition could not be created.";
else
$data["alertsuccess"]="competition created Successfully.";
$data["redirect"]="site/viewcompetition";
$this->load->view("redirect",$data);
}
}
public function editcompetition()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="editcompetition";
$data[ 'status' ] =$this->user_model->getstatusdropdown();
$data["title"]="Edit competition";
$data["before"]=$this->competition_model->beforeedit($this->input->get("id"));
$this->load->view("template",$data);
}
public function editcompetitionsubmit()
{
$access=array("1");
$this->checkaccess($access);
$this->form_validation->set_rules("id","ID","trim");
$this->form_validation->set_rules("status","Status","trim");
$this->form_validation->set_rules("name","Name","trim");
$this->form_validation->set_rules("timestamp","Timestamp","trim");
$this->form_validation->set_rules("date","Date","trim");
if($this->form_validation->run()==FALSE)
{
$data["alerterror"]=validation_errors();
$data["page"]="editcompetition";
$data[ 'status' ] =$this->user_model->getstatusdropdown();
$data["title"]="Edit competition";
$data["before"]=$this->competition_model->beforeedit($this->input->get("id"));
$this->load->view("template",$data);
}
else
{
$id=$this->input->get_post("id");
$status=$this->input->get_post("status");
$name=$this->input->get_post("name");
$timestamp=$this->input->get_post("timestamp");
$date=$this->input->get_post("date");
if($this->competition_model->edit($id,$status,$name,$timestamp,$date)==0)
$data["alerterror"]="New competition could not be Updated.";
else
$data["alertsuccess"]="competition Updated Successfully.";
$data["redirect"]="site/viewcompetition";
$this->load->view("redirect",$data);
}
}
public function deletecompetition()
{
$access=array("1");
$this->checkaccess($access);
$this->competition_model->delete($this->input->get("id"));
$data["redirect"]="site/viewcompetition";
$this->load->view("redirect",$data);
}
public function viewcompetitionparticipant()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="viewcompetitionparticipant";
$data["base_url"]=site_url("site/viewcompetitionparticipantjson");
$data["title"]="View competitionparticipant";
$this->load->view("template",$data);
}
function viewcompetitionparticipantjson()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`competo_competitionparticipant`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";
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
$maxrow=20;
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `competo_competitionparticipant`");
$this->load->view("json",$data);
}

public function createcompetitionparticipant()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="createcompetitionparticipant";
$data["title"]="Create competitionparticipant";
$this->load->view("template",$data);
}
public function createcompetitionparticipantsubmit()
{
$access=array("1");
$this->checkaccess($access);
$this->form_validation->set_rules("name","Name","trim");
if($this->form_validation->run()==FALSE)
{
$data["alerterror"]=validation_errors();
$data["page"]="createcompetitionparticipant";
$data["title"]="Create competitionparticipant";
$this->load->view("template",$data);
}
else
{
$name=$this->input->get_post("name");
if($this->competitionparticipant_model->create($name)==0)
$data["alerterror"]="New competitionparticipant could not be created.";
else
$data["alertsuccess"]="competitionparticipant created Successfully.";
$data["redirect"]="site/viewcompetitionparticipant";
$this->load->view("redirect",$data);
}
}
public function editcompetitionparticipant()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="editcompetitionparticipant";
$data["title"]="Edit competitionparticipant";
$data["before"]=$this->competitionparticipant_model->beforeedit($this->input->get("id"));
$this->load->view("template",$data);
}
public function editcompetitionparticipantsubmit()
{
$access=array("1");
$this->checkaccess($access);
$this->form_validation->set_rules("id","ID","trim");
$this->form_validation->set_rules("name","Name","trim");
if($this->form_validation->run()==FALSE)
{
$data["alerterror"]=validation_errors();
$data["page"]="editcompetitionparticipant";
$data["title"]="Edit competitionparticipant";
$data["before"]=$this->competitionparticipant_model->beforeedit($this->input->get("id"));
$this->load->view("template",$data);
}
else
{
$id=$this->input->get_post("id");
$name=$this->input->get_post("name");
if($this->competitionparticipant_model->edit($id,$name)==0)
$data["alerterror"]="New competitionparticipant could not be Updated.";
else
$data["alertsuccess"]="competitionparticipant Updated Successfully.";
$data["redirect"]="site/viewcompetitionparticipant";
$this->load->view("redirect",$data);
}
}
public function deletecompetitionparticipant()
{
$access=array("1");
$this->checkaccess($access);
$this->competitionparticipant_model->delete($this->input->get("id"));
$data["redirect"]="site/viewcompetitionparticipant";
$this->load->view("redirect",$data);
}
public function viewcompetitionscore()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="viewcompetitionscore";
$data["base_url"]=site_url("site/viewcompetitionscorejson");
$data["title"]="View competitionscore";
$this->load->view("template",$data);
}
function viewcompetitionscorejson()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`competo_competitionscore`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";
$elements[1]=new stdClass();
$elements[1]->field="`user`.`name`";
$elements[1]->sort="1";
$elements[1]->header="User";
$elements[1]->alias="user";
$elements[2]=new stdClass();
$elements[2]->field="`competo_competitionparticipant`.`name`";
$elements[2]->sort="1";
$elements[2]->header="Competition Participant";
$elements[2]->alias="competitionparticipant";
$elements[3]=new stdClass();
$elements[3]->field="`competo_competitionscore`.`score`";
$elements[3]->sort="1";
$elements[3]->header="Score";
$elements[3]->alias="score";
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
$maxrow=20;
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `competo_competitionscore` LEFT OUTER JOIN `user` ON `user`.`id`=`competo_competitionscore`.`user` LEFT OUTER JOIN `competo_competitionparticipant` ON `competo_competitionparticipant`.`id`=`competo_competitionscore`.`competitionparticipant`");
$this->load->view("json",$data);
}

public function createcompetitionscore()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="createcompetitionscore";
$data[ 'competitionparticipant' ] =$this->user_model->getcompetitionparticipantdropdown();
$data[ 'user' ] =$this->user_model->getuserdropdown();
$data["title"]="Create competitionscore";
$this->load->view("template",$data);
}
public function createcompetitionscoresubmit()
{
$access=array("1");
$this->checkaccess($access);
$this->form_validation->set_rules("user","User","trim");
$this->form_validation->set_rules("competitionparticipant","Competition Participant","trim");
$this->form_validation->set_rules("score","Score","trim");
$this->form_validation->set_rules("comments","comments","trim");
if($this->form_validation->run()==FALSE)
{
$data["alerterror"]=validation_errors();
$data["page"]="createcompetitionscore";
$data[ 'competitionparticipant' ] =$this->user_model->getcompetitionparticipantdropdown();
$data[ 'user' ] =$this->user_model->getuserdropdown();
$data["title"]="Create competitionscore";
$this->load->view("template",$data);
}
else
{
$user=$this->input->get_post("user");
$competitionparticipant=$this->input->get_post("competitionparticipant");
$score=$this->input->get_post("score");
$comments=$this->input->get_post("comments");
if($this->competitionscore_model->create($user,$competitionparticipant,$score,$comments)==0)
$data["alerterror"]="New competitionscore could not be created.";
else
$data["alertsuccess"]="competitionscore created Successfully.";
$data["redirect"]="site/viewcompetitionscore";
$this->load->view("redirect",$data);
}
}
public function editcompetitionscore()
{
$access=array("1");
$this->checkaccess($access);
$data["page"]="editcompetitionscore";
$data[ 'user' ] =$this->user_model->getuserdropdown();
$data[ 'competitionparticipant' ] =$this->user_model->getcompetitionparticipantdropdown();
$data["title"]="Edit competitionscore";
$data["before"]=$this->competitionscore_model->beforeedit($this->input->get("id"));
$this->load->view("template",$data);
}
public function editcompetitionscoresubmit()
{
$access=array("1");
$this->checkaccess($access);
$this->form_validation->set_rules("id","ID","trim");
$this->form_validation->set_rules("user","User","trim");
$this->form_validation->set_rules("competitionparticipant","Competition Participant","trim");
$this->form_validation->set_rules("score","Score","trim");
$this->form_validation->set_rules("comments","comments","trim");
if($this->form_validation->run()==FALSE)
{
$data["alerterror"]=validation_errors();
$data["page"]="editcompetitionscore";
$data[ 'competitionparticipant' ] =$this->user_model->getcompetitionparticipantdropdown();
$data[ 'user' ] =$this->user_model->getuserdropdown();
$data[ 'user' ] =$this->user_model->getuserdropdown();
$data["title"]="Edit competitionscore";
$data["before"]=$this->competitionscore_model->beforeedit($this->input->get("id"));
$this->load->view("template",$data);
}
else
{
$id=$this->input->get_post("id");
$user=$this->input->get_post("user");
$competitionparticipant=$this->input->get_post("competitionparticipant");
$score=$this->input->get_post("score");
$comments=$this->input->get_post("comments");
if($this->competitionscore_model->edit($id,$user,$competitionparticipant,$score,$comments)==0)
$data["alerterror"]="New competitionscore could not be Updated.";
else
$data["alertsuccess"]="competitionscore Updated Successfully.";
$data["redirect"]="site/viewcompetitionscore";
$this->load->view("redirect",$data);
}
}
public function deletecompetitionscore()
{
$access=array("1");
$this->checkaccess($access);
$this->competitionscore_model->delete($this->input->get("id"));
$data["redirect"]="site/viewcompetitionscore";
$this->load->view("redirect",$data);
}

}
?>
