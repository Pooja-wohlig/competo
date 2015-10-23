<div class="row">
<div class="col s12">
<h4 class="pad-left-15">Create competitionscore</h4>
</div>
<form class='col s12' method='post' action='<?php echo site_url("site/createcompetitionscoresubmit");?>' enctype= 'multipart/form-data'>
<div class=" row">
<div class=" input-field col s6">
<?php echo form_dropdown("user",$user,set_value('user'));?>
<label>User</label>
</div>
</div>
<div class=" row">
<div class=" input-field col s6">
<?php echo form_dropdown("competitionparticipant",$competitionparticipant,set_value('competitionparticipant'));?>
<label>Competition Participant</label>
</div>
</div>
<div class="row">
<div class="input-field col s6">
<label for="Score">Score</label>
<input type="text" id="Score" name="score" value='<?php echo set_value('score');?>'>
</div>
</div>
<div class="row">
<div class="input-field col s12">
<textarea name="comments" class="materialize-textarea" length="400"><?php echo set_value( 'comments');?></textarea>
<label>comments</label>
</div>
</div>
<div class="row">
<div class="col s12 m6">
<button type="submit" class="btn btn-primary waves-effect waves-light blue darken-4">Save</button>
<a href="<?php echo site_url("site/viewcompetitionscore"); ?>" class="btn btn-secondary waves-effect waves-light red">Cancel</a>
</div>
</div>
</form>
</div>
