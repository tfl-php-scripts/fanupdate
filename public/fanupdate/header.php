<?php
/*****************************************************************************
 * FanUpdate
 * Copyright (c) Jenny Ferenc <jenny@prism-perfect.net>
 * Copyright (c) 2020 by Ekaterina (contributor) http://scripts.robotess.net
*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ******************************************************************************/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<title><?php if (isset($pageTitle)) { 
    echo $pageTitle.' | ';
    $pageID = strtolower(str_replace(' ', '-', $pageTitle));
} ?>FanUpdate ADMIN</title>

<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" title="FanUpdate <?php echo $this->getOpt('version'); ?>" />
<link rel="shortcut icon" href="favicon.ico" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex,nofollow" />

<script src="js/fanupdate.js" type="text/javascript"></script>
<script src="js/fanupdate-admin.js" type="text/javascript"></script>
<script src="js/standardista-table-sorting.js" type="text/javascript"></script>

</head>

<body<?php if (!empty($pageID)) { echo ' id="'.$pageID.'"'; } ?>>

<div id="wrap">

<div id="header">
<h1><a href="index.php"><span>FanUpdate</span></a></h1>
</div>

<div id="main">
