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

struct __zone_def {
	char * name;
	char * file;
	struct __zone_def * next;
};
 
struct __named_conf {
	char * directory;
	struct __zone_def * zone;
};
 
struct dns_data {
	char * named_conf_file;
	char * zonefile;
	char * soa;
	char * full_name;
	char * domain_zone;
	char * domain_name;
	char * TTL;
	char * email;
	char * serial;
	char * refresh;
	char * retry;
	char * expire;
	char * minimum;
	char * ns1;
	char * ns1full;
	char * ns1ip;
	char * ns2;
	char * ns2full;
	char * ns2ip;
	char * mx;
	char * mxip;
};

/*
unsigned int add_dns_record_by_1(struct dns_data * DNS, unsigned int mx_state);
unsigned int add_dns_record_by_2(struct dns_data * DNS, unsigned int mx_state);
unsigned int add_dns_record_by_3(struct dns_data * DNS, unsigned int mx_state);
*/

char * dns_find_zone_file(char * z_name);
unsigned int dns_zone_present(char * z_name);
unsigned int dns_read_named_conf();
unsigned int dns_add_domain(unsigned int id);
unsigned int dns_create_zone(struct __p_answer * domain);
