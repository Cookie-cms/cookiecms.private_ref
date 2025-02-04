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
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php"; // Define your variables like client_id, secret_id, etc.
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/DiscordModule.php";

// Define the URL array with the link key
$url = array("link" => url()); // Or you can use the shorthand: $url = ["link" => url()];

return response(null, false, 200, null, $url);
