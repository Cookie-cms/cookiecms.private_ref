<?php
# This file is part of CookieCms.
#
# CookieCms is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# CookieCms is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with CookieCms. If not, see <http://www.gnu.org/licenses/>.

// require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";
require $_SERVER['DOCUMENT_ROOT'] ."/src/vendor/autoload.php";
use Symfony\Component\Yaml\Yaml;

function read_yaml($file_path) {


    if (!file_exists($file_path)) {
        throw new Exception("File not found: $file_path");
    }

    $yaml_content = file_get_contents($file_path);

    if ($yaml_content === false) {
        throw new Exception("Error reading file: $file_path");
    }

    $data = Yaml::parse($yaml_content);

    if ($data === false && $yaml_content !== '') {
        throw new Exception("Error parsing YAML in file: $file_path");
    }

    return $data ?? [];
}

?>
