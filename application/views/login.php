<?php $this->load->view('common/header'); ?>
<div class="form-tabbed form-tabbed-manyforms">
    <?php echo validation_errors(); ?>
    <ul class="form-tab-navigation">
        <li><a href="javascript:;" class="form-tab-navigation-link active">Login</a></li>
        <li><a href="javascript:;" class="form-tab-navigation-link">Reset password</a></li>
    </ul>
    <?php echo form_open('login', array('id' => 'login-form', 'class'=>'form-awesome round-10 form-tab active')); ?>
        <ul>
            <li>
                <label for="userName">Username:</label>
                <?php echo form_input(array('name'=>'userName', 'id'=>'userName', 'value'=>set_value('userName'))); ?>
            </li>
            <li>
                <label for="userPassword">Password:</label>
                <?php echo form_password(array('name'=>'userPassword', 'id'=>'userPassword')); ?>
            </li>
            <li class="right">
                <button type="submit"><span>Login</span></button>
            </li>
            <li>
                <?php echo form_hidden('ref', $referrer); ?>
            </li>
        </ul>
    <?php echo form_close(); ?>
    <?php echo form_open('login/recover', array('class'=>'form-awesome round-10 form-tab')); ?>
        <ul>
            <li>
                <label for="userEmail">E-mail:</label>
                <?php echo form_input(array('name'=>'userEmail', 'id'=>'userEmail')); ?>
            </li>
            <li class="right">
                <button type="submit"><span>Reset password</span></button>
            </li>
        </ul>
    <?php echo form_close(); ?>
</div>
<?php $this->load->view('common/footer'); ?>