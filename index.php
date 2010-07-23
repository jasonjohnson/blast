<?php

include 'lib/bootstrap.php';

Application::map('(.*)', 'Welcome');

Application::run();

?>