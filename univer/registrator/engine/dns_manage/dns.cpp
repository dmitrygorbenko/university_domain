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

#include "define.h"
#include "message.h"
#include "str.h"
#include "sql.h"
#include "dns.h"

using namespace std;

struct __named_conf * named_conf = NULL;

unsigned int dns_read_named_conf()
{
	char * str = NULL;
	char * first = NULL;
	char * temp = NULL;
	char * zone_name = NULL;
	char * zone_file = NULL;
	FILE * file = NULL;
	unsigned int find_end;
	struct __zone_def * zone = NULL;

	named_conf = new __named_conf;
	named_conf->directory = NULL;
	named_conf->zone = NULL;

	file = fopen(NAMED_FILE_NAME, "r");

	if (file == NULL) {
		printf("Не могу открыть файл \"named.conf\", заявленый в конфиге.<br>");
		return RES_FALSE;
	}

	while (!feof(file)) {

		str = read_string(file);

		if (!str)
			break;

		first = get_word_by_number(str, 1);
		first = clear_white_spaces_abae(first);

		if (strstr(first, "directory")) {
			temp = get_word_by_number(str, 2);
			named_conf->directory = cut_quotes(temp);
			safe_free(&temp);
		}

		if (strstr(first, "zone")) {
			find_end = FALSE;

			temp = get_word_by_number(str, 2);
			zone_name = cut_quotes(temp);
			safe_free(&temp);

			safe_free(&str);

			while(find_end == FALSE) {

				str = read_string(file);

				if (!str)
					break;

				first = get_word_by_number(str, 1);
				first = clear_white_spaces_abae(first);

				if (strstr(first, "file")) {
					temp = get_word_by_number(str, 2);
					zone_file = cut_quotes(temp);
					safe_free(&temp);
					find_end = TRUE;
				}

				if (strstr(first, "}")) {
					find_end = TRUE;
				}

				safe_free(&first);
				safe_free(&str);
			}

			if (zone_file) {
				zone = new __zone_def;

				zone->file = zone_file;
				zone->name = zone_name;

				zone->next = named_conf->zone;
				named_conf->zone = zone;
			}
			else {
				safe_free(&zone_name);
			}
		}

		if (str)
			safe_free(&str);
		if (first)
			safe_free(&first);
	}

	fclose(file);

	return RES_TRUE;
};

char * dns_find_zone_file(char * z_name)
{
	char * result = NULL;
	struct __zone_def * zone = NULL;

	zone = named_conf->zone;

	while(zone) {
		if (strcmp(z_name, zone->name) == 0) {
			result = strdup(zone->name);
			break;
		}
		zone = zone->next;
	}

	return result;
}

unsigned int dns_zone_present(char * z_name)
{
	unsigned int result = FALSE;
	struct __zone_def * zone = NULL;

	zone = named_conf->zone;

	while(zone) {
		if (strcmp(z_name, zone->name) == 0) {
			result = TRUE;
			break;
		}
		zone = zone->next;
	}

	return result;
}

unsigned int dns_create_zone(struct __p_answer * domain)
{
	FILE * file;

	file = fopen(NAMED_FILE_NAME, "a+");

	if (file == NULL) {
		printf("Не могу открыть файл \"named.conf\", заявленый в конфиге.<br>");
		return RES_FALSE;
	}
	
	printf("<pre>\n");
	printf("zone \"%s.%s\" IN {\n", domain->d_name, domain->z_name);
	printf("	type master;\n");
	printf("	file \"%s.%s\";\n", domain->d_name, domain->z_name);
	printf("	allow-transfer {\n");
	printf("		%s;\n", "192.168.0.2");
	printf("	};\n");
	printf("};\n");
	printf("</pre>\n");

	fclose(file);
	
	return RES_FALSE;
};

unsigned int dns_add_domain(unsigned int id)
{
	char * zone_file_name = NULL;
	struct __p_answer * domain = NULL;

	domain = parse_result("view_query_body_individ", id);

	if (domain->result_code == SQL_RESULT_ERROR) {
		printf("<tr><td class=td_data_l colspan=2>Caught Error: %s<br>Query: %s</td></tr>\n", domain->errmsg, domain->source_query);
		return RES_FALSE;
	}
	if (domain->result_code == SQL_RESULT_EMPTY) {
		printf("<tr><td class=td_data_l colspan=2>class=error>В таблице нет данных (информация о домене)</td></tr>\n");
		return RES_FALSE;
	}

	if (domain->domain_status != domain->enum_wait_for_pay) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> не в очереди на оплату - пропускаю</td></tr>\n", domain->d_name, domain->z_name);
		return RES_TRUE;
	}

	if (dns_zone_present(domain->z_name) == FALSE) {
		if (dns_create_zone(domain) == RES_FALSE) {
			printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> не могу создать зону для домена - пропускаю</td></tr>\n", domain->d_name, domain->z_name);
			return RES_FALSE;
		}
	}
	else {
	
	}

	printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_green> поставлен на регистрацию</td></tr>\n", domain->d_name, domain->z_name);

	return RES_TRUE;
}
