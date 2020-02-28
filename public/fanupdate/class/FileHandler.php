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

class FileHandler
{

    public $_allowed_ext = array();

    public function __construct($allowed_ext = array())
    {
        if (!empty($allowed_ext)) {
            $this->_allowed_ext = $allowed_ext;
        }
    }

    public function getExtension($filename)
    {
        return (strpos($filename, '.') === false) ? '' : strtolower(substr(strrchr($filename, '.'), 1));
    }

    public function getMimeType($filename)
    {
        $file_extension = $this->getExtension($filename);
        switch ($file_extension) {
            case 'mp3':
                $ctype = 'audio/mpeg';
                break;
            case 'm4a':
                $ctype = 'audio/x-m4a';
                break;
            case 'mp4':
                $ctype = 'video/mp4';
                break;
            case 'm4v':
                $ctype = 'video/x-m4v';
                break;
            case 'mov':
                $ctype = 'video/quicktime';
                break;
            case 'avi':
                $ctype = 'video/x-msvideo';
                break;
            case 'rtf':
                $ctype = 'application/rtf';
                break;
            case 'pdf':
                $ctype = 'application/pdf';
                break;
            case 'exe':
                $ctype = 'application/octet-stream';
                break;
            case 'zip':
                $ctype = 'application/x-zip-compressed';
                break;
            case 'doc':
                $ctype = 'application/msword';
                break;
            case 'xls':
                $ctype = 'application/vnd.ms-excel';
                break;
            case 'ppt':
                $ctype = 'application/vnd.ms-powerpoint';
                break;
            case 'gif':
                $ctype = 'image/gif';
                break;
            case 'png':
                $ctype = 'image/png';
                break;
            case 'jpe':
            case 'jpeg':
            case 'jpg':
                $ctype = 'image/jpg';
                break;
            default:
                $ctype = 'application/force-download';
        }
        return $ctype;
    }

    // get rid of bad characters
    public function fixFilename($orig_name)
    {
        $orig_name = str_replace(' ', '_', $orig_name);
        return preg_replace('/[^-_.0-9a-zA-Z]/', '', $orig_name);
    }

    public function getDimensions($path)
    {
        [$width, $height] = getimagesize($path);
        return $width . 'x' . $height;
    }

    public function getSize($path)
    {
        return filesize($path);
    }

    public function getHumanSize($path)
    {
        if (file_exists($path)) {
            $filesizename = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
            $size = $this->getSize($path);
            return round($size / (1024 ** ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
        }

        return 'File Not Found';
    }

    public function checkExtension($filename)
    {
        $filenameext = $this->getExtension($filename);

        foreach ($this->_allowed_ext as $xValue) {
            if ($filenameext == $xValue) {
                return true;
            }
        }

        return false;
    }
}
