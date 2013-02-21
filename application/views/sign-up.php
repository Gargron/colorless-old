<?php $this->load->view('common/header'); ?>
<?php echo validation_errors(); ?>
<?php echo form_open('login/register/'.$this->uri->segment(3), array('class'=>'form-awesome round-10'), array('url' => '')); ?>
    <!--<ul>
        <li>
            <label for="userName">Username:</label>
            <?php echo form_input(array('name'=>'userName', 'id'=>'userName', 'value'=>set_value('userName'), 'placeholder'=>'Example_123')); ?>
        </li>
        <li>
            <label for="userEmail">E-mail:</label>
            <?php echo form_input(array('name'=>'userEmail', 'id'=>'userEmail', 'value'=>set_value('userEmail', $userEmail), 'autocomplete'=>'off', 'type'=>'email', 'placeholder'=>'example@domain.com')); ?>
        </li>
        <li>
            <label for="userPassword">Password:</label>
            <?php echo form_password(array('name'=>'userPassword', 'id'=>'userPassword', 'autocomplete'=>'off')); ?>
        </li>
        <li class="right">
            <button type="submit"><span>Join</span></button>
        </li>
        <li style="width:60%">
            <table>
                <tr>
                    <td>
                        <label for="userAgrees">I agree to <?php echo anchor('about/rules', 'the few rules'); ?></label>
                    </td>
                    <td>
                        <?php echo form_checkbox(array('name'=>'userAgrees', 'id'=>'userAgrees')); ?>
                    </td>
                </tr>
            </table>
        </li>
    </ul-->
    <p>
Hey, we're going to have registrations closed until 0:00AM EST on Christmas Day. 

Sorry for the troubles!
<a href="http://thecolorless.net/thread/557589#p557589">More Information</a>
</p>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>