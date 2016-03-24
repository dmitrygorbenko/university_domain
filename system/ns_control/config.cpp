/***************************************************************************
 *   Copyright (C) 2006 by Dmitriy Gorbenko                                *
 *   nial@ukr.net                                                          *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   You should have received a copy of the GNU General Public License     *
 *   along with this program; if not, write to the                         *
 *   Free Software Foundation, Inc.,                                       *
 *   59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
 ***************************************************************************/

#include <ctype.h>
#include <cstring>
#include <cstdio>
#include <cstdlib>

#include "define.h"
#include "message.h"
#include "str.h"
#include "config.h"

using namespace std;

struct config_state_def config_state;
struct config_data_def config_data;

unsigned int config_init(char * file_name)
{
	config_state.cfg_exists = TRUE;
	config_state.cfg_read_startup = TRUE;
	config_state.cfg_fd = NULL;
	config_state.cfg_file_name = safe_strdup(file_name);

	config_data.listen_ip = NULL;
	config_data.listen_port = 0;
	config_data.allow_from = NULL;
	config_data.named_zone_file_list = NULL;
	config_data.named_zone_dir = NULL;
	config_data.ns_server = NULL;
	config_data.ns2_server = NULL;
	config_data.mx_server = NULL;
	config_data.mx2_server = NULL;
	config_data.soa_email_addr = NULL;
	config_data.primary_ip = NULL;

	config_state.cfg_fd = fopen(config_state.cfg_file_name, "r");

	if (config_state.cfg_fd == NULL) {
		message("Config_Init: Can't open Config file.\nConfig file name: %s\nPlease, create this file.\n", config_state.cfg_file_name);
		config_state.cfg_exists = FALSE;
		config_state.cfg_read_startup = FALSE;
	}

	if(config_state.cfg_fd != NULL)
		fclose(config_state.cfg_fd);

	if(!config_state.cfg_read_startup)
		return FALSE;

	return TRUE;
};

void config_shutdown()
{
	if (config_state.cfg_file_name)
		safe_free(&config_state.cfg_file_name);

	if (config_data.listen_ip)
		safe_free(&config_data.listen_ip);

	if (config_data.allow_from)
		safe_free(&config_data.allow_from);

	if (config_data.named_zone_file_list)
		safe_free(&config_data.named_zone_file_list);

	if (config_data.named_zone_dir)
		safe_free(&config_data.named_zone_dir);

	if (config_data.ns_server)
		safe_free(&config_data.ns2_server);

	if (config_data.mx_server)
		safe_free(&config_data.mx_server);

	if (config_data.mx2_server)
		safe_free(&config_data.mx2_server);

	if (config_data.soa_email_addr)
		safe_free(&config_data.soa_email_addr);

	if (config_data.primary_ip)
		safe_free(&config_data.primary_ip);
};

void config_close()
{
	if (config_state.cfg_open == TRUE)
		fclose(config_state.cfg_fd);
};

unsigned int read_config()
{
	FILE * file = NULL;
	char * filename = NULL;
	char * str = NULL;
	char * tmp = NULL;
	unsigned char result = 0;

	if (!config_state.cfg_read_startup)
		return FALSE;

	result = TRUE;

	filename = config_state.cfg_file_name;

	file = fopen(filename,"r");
	config_state.cfg_open = TRUE;

	if (file == NULL) {
		message("Read_Config: Can't open Config file.\nConfig file mame: %s",
			config_state.cfg_file_name);
		config_state.cfg_exists = FALSE;
		config_state.cfg_read_startup = FALSE;
		return FALSE;
	}

	while (!feof(file)) {

		str = read_string(file);

		if (!str)
			break;

		if ((strstr(str, "listen_ip") != (char) NULL))
			goto listen_ip;
		if ((strstr(str, "listen_port") != (char) NULL))
			goto listen_port;
		if ((strstr(str, "allow_from") != (char) NULL))
			goto allow_from;
		if ((strstr(str, "named_zone_file_list") != (char) NULL))
			goto named_zone_file_list;
		if ((strstr(str, "named_zone_dir") != (char) NULL))
			goto named_zone_dir;
		if ((strstr(str, "ns_server") != (char) NULL))
			goto ns_server;
		if ((strstr(str, "ns2_server") != (char) NULL))
			goto ns2_server;
		if ((strstr(str, "mx_server") != (char) NULL))
			goto mx_server;
		if ((strstr(str, "mx2_server") != (char) NULL))
			goto mx2_server;
		if ((strstr(str, "soa_email_addr") != (char) NULL))
			goto soa_email_addr;
		if ((strstr(str, "primary_ip") != (char) NULL))
			goto primary_ip;

		goto freedom;

listen_ip:
		if (config_data.listen_ip)
			safe_free(&config_data.listen_ip);
		config_data.listen_ip = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

listen_port:
		tmp = get_param(str);
		config_data.listen_port = atoi(tmp);
		if (tmp)
			safe_free(&tmp);
		if (str)
			safe_free(&str);
		goto freedom;

allow_from:
		if (config_data.allow_from)
			safe_free(&config_data.allow_from);
		config_data.allow_from = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

named_zone_file_list:
		if (config_data.named_zone_file_list)
			safe_free(&config_data.named_zone_file_list);
		config_data.named_zone_file_list = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

named_zone_dir:
		if (config_data.named_zone_dir)
			safe_free(&config_data.named_zone_dir);
		config_data.named_zone_dir = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

ns_server:
		if (config_data.ns_server)
			safe_free(&config_data.ns_server);
		config_data.ns_server = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

ns2_server:
		if (config_data.ns2_server)
			safe_free(&config_data.ns2_server);
		config_data.ns2_server = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

mx_server:
		if (config_data.mx_server)
			safe_free(&config_data.mx_server);
		config_data.mx_server = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

mx2_server:
		if (config_data.mx2_server)
			safe_free(&config_data.mx2_server);
		config_data.mx2_server = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

soa_email_addr:
		if (config_data.soa_email_addr)
			safe_free(&config_data.soa_email_addr);
		config_data.soa_email_addr = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;
primary_ip:
		if (config_data.primary_ip)
			safe_free(&config_data.primary_ip);
		config_data.primary_ip = get_param(str);
		if (str)
			safe_free(&str);
		goto freedom;

freedom:
		if(str)
			safe_free(&str);
	}

	config_state.cfg_open = FALSE;
	if(file != NULL)
		fclose(file);

	if (result == FALSE)
		return FALSE;

	return TRUE;
};

unsigned int check_config()
{
	unsigned int result = TRUE;

	if (config_data.listen_ip == NULL) {
		message("Please, insert 'listen_ip = xxx.xxx.xxx.xxx' to config file\n");
		result = FALSE;
	}

	if (config_data.listen_port == 0) {
		message("Please, insert 'listen_port = <some_port>' to config file\n");
		result = FALSE;
	}

	if (config_data.allow_from == NULL) {
		message("Please, insert 'allow_from = xxx.xxx.xxx.xxx, ...' to config file\n");
		result = FALSE;
	}

	if (config_data.named_zone_file_list == NULL) {
		message("Please, insert 'named_zone_file_list = <some_path>' to config file\n");
		result = FALSE;
	}

	if (config_data.named_zone_dir == NULL) {
		message("Please, insert 'named_zone_dir = <some_path>' to config file\n");
		result = FALSE;
	}

	if (config_data.ns_server == NULL) {
		message("Please, insert 'ns_server = xxx.xxx.xxx.xxx' to config file\n");
		result = FALSE;
	}

	if (config_data.ns2_server == NULL) {
		message("Please, insert 'ns2_server = xxx.xxx.xxx.xxx' to config file\n");
		result = FALSE;
	}

	if (config_data.mx_server == NULL) {
		message("Please, insert 'mx_server = xxx.xxx.xxx.xxx' to config file\n");
		result = FALSE;
	}

	if (config_data.mx2_server == NULL) {
		message("Please, insert 'mx2_server = xxx.xxx.xxx.xxx' to config file\n");
		result = FALSE;
	}

	if (config_data.soa_email_addr == NULL) {
		message("Please, insert 'soa_email_addr = <some_value>' to config file\n");
		result = FALSE;
	}

	if (config_data.primary_ip == NULL) {
		message("Please, insert 'primary_ip = xxx.xxx.xxx.xxx' to config file\n");
		result = FALSE;
	}

	return result;
}

