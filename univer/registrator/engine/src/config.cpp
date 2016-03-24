/***************************************************************************
 *   Copyright (C) 2003, 2004, 2005                                              *
 *   by Dmitriy Gorbenko. "XAI" University, Kharkov, Ukraine               *
 *   e-mail: nial@ukr.net                                                  *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 ***************************************************************************/

#ifdef HAVE_CONFIG_H
#include <config.h>
#include <cstring>
#endif

#include <cstdio>
#include <cstdlib>
#include <ctime>
#include <ctype.h>

#include "define.h"
#include "message.h"
#include "conf.h"
#include "str.h"

using namespace std;

struct config_state cfg_state;
struct config_data cfg_data;

unsigned int config_init(char * file_name)
{
	cfg_state.cfg_exists = TRUE;
	cfg_state.cfg_read_startup = TRUE;
	cfg_state.cfg_desc = NULL;
	cfg_state.cfg_file_name = strdup(file_name);

	cfg_data.named_conf_file = NULL;
	cfg_data.mx = NULL;
	cfg_data.ns1 = NULL;
	cfg_data.ns2 = NULL;
	cfg_data.zone = NULL;
	cfg_data.ttl = NULL;
	cfg_data.mail = NULL;
	cfg_data.refresh = NULL;
	cfg_data.retry = NULL;
	cfg_data.expire = NULL;
	cfg_data.minimum = NULL;

	cfg_state.cfg_desc = fopen(cfg_state.cfg_file_name,"r");

    	if (cfg_state.cfg_desc == NULL)
    	{
		message("Error: config.c: config_init: Can't open Config file.\nConfig file mame: ");
		message(cfg_state.cfg_file_name);
		message("\nPlease, create this file.\n");
		cfg_state.cfg_exists = FALSE;
		cfg_state.cfg_read_startup = FALSE;
	}

	if(cfg_state.cfg_desc != NULL)
		fclose(cfg_state.cfg_desc);

	if(!cfg_state.cfg_read_startup)
		return RES_FALSE;

	return RES_TRUE;
};

void config_shutdown()
{
	if (cfg_state.cfg_file_name)
		safe_free(&cfg_state.cfg_file_name);

	if (cfg_data.named_conf_file)
		safe_free(&cfg_data.named_conf_file);
	if (cfg_data.mx)
		safe_free(&cfg_data.mx);
	if (cfg_data.ns1)
		safe_free(&cfg_data.ns1);
	if (cfg_data.ns2)
		safe_free(&cfg_data.ns2);
	if (cfg_data.zone)
		safe_free(&cfg_data.zone);
	if (cfg_data.ttl)
		safe_free(&cfg_data.ttl);
	if (cfg_data.mail)
		safe_free(&cfg_data.mail);
	if (cfg_data.refresh)
		safe_free(&cfg_data.refresh);
	if (cfg_data.retry)
		safe_free(&cfg_data.retry);
	if (cfg_data.expire)
		safe_free(&cfg_data.expire);
	if (cfg_data.minimum)
		safe_free(&cfg_data.minimum);
};

void config_close()
{
	if (cfg_state.cfg_open)
		fclose(cfg_state.cfg_desc);
};

unsigned int read_config()
{
	FILE * file;
	char * filename;
	char * str;
	char * buf;
	unsigned char result;

	if (!cfg_state.cfg_read_startup)
		return RES_FALSE;

	result = TRUE;

	filename = cfg_state.cfg_file_name;

	file = fopen(filename,"r");
	cfg_state.cfg_open = TRUE;

	if (file == NULL) {
		message("Error: config.c: read_config: Can't open Config file.\nConfig file mame: ");
		message(cfg_state.cfg_file_name);
		cfg_state.cfg_exists = FALSE;
		cfg_state.cfg_read_startup = FALSE;
		return RES_FALSE;
	}

	while (!feof(file)) {

		str = read_string(file);

		if (!str)
			break;

		if ((strstr(str, "named_conf_file") != (char) NULL))
			goto named_conf_file;
		if ((strstr(str, "default_mx") != (char) NULL))
			goto mx;
		if ((strstr(str, "default_ns1") != (char) NULL))
			goto ns1;
		if ((strstr(str, "default_ns2") != (char) NULL))
			goto ns2;
		if ((strstr(str, "default_ttl") != (char) NULL))
			goto ttl;
		if ((strstr(str, "default_mail") != (char) NULL))
			goto mail;
		if ((strstr(str, "default_refresh") != (char) NULL))
			goto refresh;
		if ((strstr(str, "default_retry") != (char) NULL))
			goto retry;
		if ((strstr(str, "default_expire") != (char) NULL))
			goto expire;
		if ((strstr(str, "default_minimum") != (char) NULL))
			goto minimum;
		if ((strstr(str, "default_zone") != (char) NULL))
			goto zone;

		goto freedom;

named_conf_file:
		cfg_data.named_conf_file = get_param(str);
		safe_free(&str);
		goto freedom;

mx:
		cfg_data.mx = get_param(str);
		safe_free(&str);
		goto freedom;

ns1:
		cfg_data.ns1 = get_param(str);
		safe_free(&str);
		goto freedom;

ns2:
		cfg_data.ns2 = get_param(str);
		safe_free(&str);
		goto freedom;

ttl:
		cfg_data.ttl = get_param(str);
		safe_free(&str);
		goto freedom;

mail:
		cfg_data.mail = get_param(str);
		safe_free(&str);
		goto freedom;

refresh:
		cfg_data.refresh = get_param(str);
		safe_free(&str);
		goto freedom;

retry:
		cfg_data.retry = get_param(str);
		safe_free(&str);
		goto freedom;

expire:
		cfg_data.expire = get_param(str);
		safe_free(&str);
		goto freedom;

minimum:
		cfg_data.minimum = get_param(str);
		safe_free(&str);
		goto freedom;

zone:
		cfg_data.zone = get_param(str);
		safe_free(&str);
		goto freedom;

freedom:
		if(str)
			safe_free(&str);
	}

#if defined (DEBUG) || defined (CONFIG_DEBUG)
	print_config();
#endif

	cfg_state.cfg_open = FALSE;
	if(file != NULL)
		fclose(file);

	if (result == FALSE)
		return RES_FALSE;

	return RES_TRUE;
};
