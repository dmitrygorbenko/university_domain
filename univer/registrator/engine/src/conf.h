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

struct config_state {
	unsigned int 	cfg_exists;
	unsigned int	cfg_read_startup;
	unsigned int	cfg_open;
	FILE * 	cfg_desc;
	char * 	cfg_file_name;
};

struct config_data {
	char *	named_conf_file;
	char *	mx;
	char *	ns1;
	char *	ns2;
	char *	zone;
	char *	ttl;
	char *	mail;
	char *	refresh;
	char *	retry;
	char *	expire;
	char *	minimum;
};

void print_config();
void config_shutdown();
void config_close();
unsigned int config_init(char * file_name);
unsigned int read_config();
