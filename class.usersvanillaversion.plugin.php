<?php if (!defined('APPLICATION')) exit();

$PluginInfo['UsersVanillaVersion'] = array(
	'Name' => 'Users Vanilla Version',
	'Description'	=> 'User can enter Vanilla version and it is displayed in profile',
	'Version' => '0.1',
	'Author' => 'R_J',
  'RequiredApplications' => array('Vanilla' => '>=2.0.18'),
  'RequiredTheme' => False, 
  'RequiredPlugins' => False,
  'License' => 'GPL'
);

class UsersVanillaVersionPlugin extends Gdn_Plugin
{	
  // set additional field in user table
  public function Setup()	{
    Gdn::Structure()->Table('User')
      ->Column('UsersVanillaVersion', 'varchar(32)', TRUE)
      ->Set(FALSE, FALSE);
  }
  
  // delete additional field in user table
  public function OnDisable() {
    $Database = Gdn::Database();
    $Structure = $Database->Structure();
    $Px = $Database->DatabasePrefix;
    $Structure->Query("ALTER TABLE {$Px}User drop column UsersVanillaVersion");
  }

  // extra field for version info in edit profile
  public function ProfileController_EditMyAccountAfter_Handler($Sender) {
    echo '<li>';
    echo $Sender->Form->Label(T('Vanilla Version Used'), 'UsersVanillaVersion');
    echo $Sender->Form->TextBox('UsersVanillaVersion', array('class' => 'InputBox SmallInput'));
    echo '</li>';
  }

  // show extra info in profile
	public function ProfileController_AddProfileTabs_Handler($Sender)
	{
		$UsersVanillaVersion = $Sender->User->UsersVanillaVersion;
		if(!empty($UsersVanillaVersion)) {
      $HtmlOut = '<div class="Box"><h4>Using Vanilla Version</h4>'.Gdn_Format::Text($UsersVanillaVersion).'</div>';
      $Sender->AddAsset('Panel', $HtmlOut, 'UsersVanillaVersion');
		}
	}
  
  // show extra info in discussion
  public function DiscussionController_CommentInfo_Handler($Sender) {
    $UserID = $Sender->EventArguments['Object']->InsertUserID;
    $User = Gdn::SQL()->GetWhere('User', array('UserID' => $UserID))->FirstRow(DATASET_TYPE_ARRAY);
    $UsersVanillaVersion = Gdn_Format::Text(GetValue('UsersVanillaVersion', $User));
		if(!empty($UsersVanillaVersion)) {
      echo Wrap('Uses Vanilla version '.$UsersVanillaVersion, 'div', array('class' => 'UsersVanillaVersion'));
    }
  }
}