            <!-- Static navbar -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">                        
                        <a class="navbar-brand navbar-hi-user" href="javascript:">Hi, <?php echo $_SESSION['signin']['username'] ?></a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <?php $class = strtolower($this->router->fetch_class()); ?>
                            <li class="<?php echo $class == 'manager' ? 'active' : '' ?>">
                                <a href="<?php echo base_url('manager') ?>">Manager</a>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <?php if (isset($_SESSION['signin']['username']) && $_SESSION['signin']['username'] != 'admin') { ?>
                            <li class="<?php 
                                if ($class == 'signin' && $this->router->fetch_method() == 'change_password') { 
                                    echo 'active'; 
                                }
                                ?>">
                                <a href="<?php echo base_url('signin/change_password') ?>">Change Password</a>
                            </li>
                            <?php } ?>
                            <li><a href="<?php echo base_url('signin/out') ?>">Signout</a></li>
                        </ul>                        
                    </div><!--/.nav-collapse -->
                </div><!--/.container-fluid -->
            </nav>

            <?php if (isset($alert) && $alert != '') { ?>
            <div class="alert alert-<?php echo $alert_type; ?>" role="alert"><?php echo $alert; ?></div>
            <?php } ?>
            
