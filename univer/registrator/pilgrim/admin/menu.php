<?php
	require_once ("header.php");

function print_menu()
{
	$menu = Array (
		Array ("Server", "Server Setup", "server_status.php", "general", "body", Array (
			Array("Server:Name Servers", "Name Servers", "ns.php", "body"),
			Array("Server:Mail Exchange Servers", "Mail Exchange Servers", "mx.php", "body"),
			Array("Server:Root Zones", "Root Zones", "delegation_zone.php", "body"),
			Array("Server:Create NS Server", "Create NS Server", "ns_add.php", "body"),
			Array("Server:Create MX Server", "Create MX Server", "mx_add.php", "body"),
			Array("Server:Create Root Zone", "Create Root Zone", "delegation_zone_add.php", "body")
			)
		),
		Array ("Clients", "Clients", "client.php", "support", "body", Array (
			Array("Clients:View", "Show Clients", "client.php", "body"),
			Array("Clients:Search", "Search Client", "client_search.php", "body"),
			Array("Clients:Create", "Create Client", "client_add.php", "body"),
			Array("Clients:Removed", "Show Removed Clients", "client_removed.php", "body")
			)
		),
		Array ("Domains", "Client's Zones", "zone.php", "domain", "body", Array (
			Array("Domain:Zones", "Show Zones", "zone.php", "body"),
			Array("Domain:Search", "Search Zone", "zone_search.php", "body"),
			Array("Domain:Create", "Create Zone", "zone_add.php", "body"),
			Array("Domain:Removed", "Show Removed Zones", "zone_removed.php", "body")
			)
		),
		Array ("Redirectors", "Client's Redirectors", "zone.php", "domain", "body", Array (
			Array("Redirect:View", "Show Redirectors", "redirect.php", "body"),
			Array("Redirect:Search", "Search Redirector", "redirect_search.php", "body"),
			Array("Redirect:Create", "Create Redirector", "redirect_add.php", "body"),
			Array("Redirect:Removed", "Show Removed Redirector", "redirect_removed.php", "body")
			)
		),
		Array ("Service", "Service Manage", "service_manage.php", "general", "body", Array (
			Array("Pilgrim:Overview", "System Status", "overview.php", "body"),
			Array("Pilgrim:Services", "System Services", "services.php", "body"),
			Array("Service:Restart", "Services Restart", "service_restart.php", "body")
			)
		),
		Array ("Billing", "Billing", "billing.php", "general", "body", Array (
			Array("Billing:Overview", "Billing Overview", "overview.php", "body"),
			Array("Billing:Zone", "Root Zone Setup", "services.php", "body")
			)
		),
		Array ("LOGOUT", "Logout", "logout.php", "logout", "_top")
	);

echo "
	<table width=\"200px\" border=\"0\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#F5F5F5\">";

	for ($i = 0; $i < count($menu); $i++) {
		echo "
			<!-- ".$menu[$i][0]." -->
			<tr class=\"menu\" height=\"36px\"  width=\"200px\">
				<td class=\"menu\" height=\"36px\" width=\"36px\" align=\"left\" background=\"../img/new/item_major.jpg\">
					<a href=\"".$menu[$i][2]."\" target=\"".$menu[$i][4]."\">
						<img class=\"menu_img\" height=\"36px\" width=\"36px\" src=\"../img/menu/".$menu[$i][3]."_a.gif\" name=\"".$menu[$i][3]."\" id=\"".$menu[$i][3]."\">
					</a>
				</td>
				<td class=\"menu\" height=\"36px\" width=\"163px\" background=\"../img/new/item_major.jpg\">
					<a class=\"menu\" href=\"".$menu[$i][2]."\" target=\"".$menu[$i][4]."\">
						".$menu[$i][1]."
					</a>
				</td>
			</tr>
			";

		if (isset($menu[$i][5]))
			for ($k = 0; $k < count($menu[$i][5]); $k++)
				echo "
				<!-- ".$menu[$i][5][$k][0]." -->
				<tr width=\"200px\">
					<td colspan=\"2\" width=\"200px\" background=\"../img/menu/open_background.jpg\">
						<a class=\"submenu\" href=\"".$menu[$i][5][$k][2]."\" target=\"".$menu[$i][5][$k][3]."\">
							".$menu[$i][5][$k][1]."
						</a>
					</td>
				</tr>
				";
	}
	echo "</table>";
};

	html_header("menu");

	print_menu();

	html_end();

?>
