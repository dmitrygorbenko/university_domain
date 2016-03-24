/***************************************************************************
 *   Copyright (C) 2003, 2004, 2005                                        *
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
#include <unistd.h>
#include <sys/types.h>


#include "param.h"
#include "html.h"
#include "define.h"
#include "message.h"
#include "str.h"
#include "sql.h"
#include "common.h"
#include "conf.h"

using namespace std;

//struct { uid_t real; uid_t effective; } uid_startup;
//extern struct config_data cfg_data;

/*
unsigned int become_root()
{
	uid_t real;
	uid_t effective;
	
	real = getuid();
	effective = geteuid();

	uid_startup.real = real;
	uid_startup.effective = effective;
		
	if (effective != 1000) {
		printf("<span class=error_simple>\n");
		printf("Real UID: %d<br>\n", real);
		printf("Effective UID: %d<br>\n", effective);
		printf("Sorry, but effective uid must be set to zero (owned by root). Supply %s with sticky bit and set owner to root.<br>\n", PROGRAM_NAME);
		printf("</span>\n");
		return RES_FALSE;
	}
	
	// Becoming root...
	
	if (setreuid(effective, effective) == -1) {
		printf("<span class=error_simple>\n");
		printf("Real UID: %d<br>\n", real);
		printf("Effective UID: %d<br>\n", effective);
		printf("Can't become root. I don't know the problem. Sorry.<br>\n");
		printf("</span>\n");
		return RES_FALSE;
	}
	
	return RES_TRUE;
};

void become_simple()
{
	setreuid(uid_startup.real, uid_startup.effective);
};
*/
unsigned int check_for_ip(char * str)
{
	unsigned int result = RES_TRUE;
	unsigned int i = 0;

	unsigned int len1 = 0;

	len1 = strlen(str);

	for (i=0; i < len1 && result == RES_TRUE; i++)
		if (!isdigit(str[i]) && str[i] != '.')
			result = RES_FALSE;

	return result;
};

unsigned int check_for_ip_2(char * str1, char * str2)
{
	unsigned int result = RES_TRUE;
	unsigned int i = 0;

	unsigned int len1 = 0;
	unsigned int len2 = 0;

	len1 = strlen(str1);
	len2 = strlen(str2);

	for (i=0; i < len1 && result == RES_TRUE; i++)
		if (!isdigit(str1[i]) && str2[i] != '.')
			result = RES_FALSE;

	for (i=0; i < len2 && result == RES_TRUE; i++)
		if (!isdigit(str2[i]) && str2[i] != '.')
			result = RES_FALSE;

	return result;
};

/*
unsigned int make_all_for_delegation_need()
{
	struct dns_data DNS;
	struct dns_def DNS_DEF;
	FILE * dns_c;

	MYSQL_ROW row;
	char * query = NULL;
	char * id_table = NULL;

	char * dname = NULL;
	char * dzone = NULL;
	char * mail = NULL;
	char * ttl = NULL;
	char * serial = NULL;
	char * refresh = NULL;
	char * retry = NULL;
	char * expire = NULL;
	char * minimum = NULL;
	char * mx = NULL;
	char * ns1 = NULL, *ns1ip = NULL;
	char * ns2 = NULL, *ns2ip = NULL;

	unsigned int ns_situation = 0;
	unsigned int mx_situation = 0;
	unsigned int result = 0;

	//********************************************
	// CHECK FOR NS AND MX SERVERS

	if (config_init(CONFIG_FILE_NAME) == RES_FALSE) {
		message("<br>FATAL ERROR: Config doesn't load.<br>");
		return RES_FALSE;
	}

	read_config();
	config_close();

	id_table = get_value("ID_TABLE");

	if (!id_table) {
		printf("<br><center>Произошла ошибка. Не был указан id таблицы</center>");
		return RES_FALSE;
	}

	query = strdup("select * from query_data where id_table = ");
	query = sts (&query, id_table);

	sql_state.state = mysql_query(sql_state.connection, query);

	if (sql_state.state) {
		printf("<br><center>Произошла ошибка выбоки данных с таблицы</center>");
		return RES_FALSE;
	}

	sql_state.result = mysql_store_result(sql_state.connection);

	row = mysql_fetch_row(sql_state.result);

	if (sql_state.result && row) {
		ttl = strdup(cfg_data.ttl);

		dname = strdup(row[10]);
		if (how_much_words(dname) == 0)
			safe_free(&dname);

		dzone = strdup(row[11]);
		if (how_much_words(dzone) == 0)
			safe_free(&dzone);

		mail = strdup(cfg_data.mail);
		serial = strdup("2004111300");
		refresh = strdup(cfg_data.refresh);
		retry = strdup(cfg_data.retry);
		expire = strdup(cfg_data.expire);
		minimum = strdup(cfg_data.minimum);

		mx = strdup(row[17]);
		if (how_much_words(mx) == 0)
			safe_free(&mx);

		ns1 = strdup(row[15]);
		if (how_much_words(ns1) == 0)
			safe_free(&ns1);

		ns2 = strdup(row[16]);
		if (how_much_words(ns2) == 0)
			safe_free(&ns2);

	}
	else {
		printf("<br><center>Произошла ошибка. Был указан неверный id таблицы</center>");
		safe_free(&query);
		safe_free(&id_table);
		return RES_FALSE;
	}

	if (!ns1 || !ns2)
		ns_situation = 1;
	else {
		result = check_for_ip_2(ns1, ns2);

		if (result == RES_TRUE)
			ns_situation = 3;
		else
			ns_situation = 2;
	}

	if (!mx)
		mx_situation = 1;
	else {
		result = check_for_ip(mx);

		if (result == RES_TRUE)
			mx_situation = 3;
		else
			mx_situation = 2;
	}
//
//	printf("MX: %d, NS: %d<br>", mx_situation, ns_situation);
//	printf("Mx: %s<br>", mx);
//	printf("NS1: %s<br>", ns1);
//	printf("NS2: %s<br>", ns2);
//
	//********************************************
	// INITIALIZATION DATA

	DNS_DEF.mx = strdup(cfg_data.mx);
	DNS_DEF.ns1 = strdup(cfg_data.ns1);
	DNS_DEF.ns2 = strdup(cfg_data.ns2);
	DNS_DEF.zone = strdup(cfg_data.zone);

	DNS.named_conf_file = strdup(cfg_data.named_conf_file);

	DNS.TTL = strdup(ttl);
	DNS.domain_name = strdup(dname);
	DNS.domain_zone = strdup(dzone);

	DNS.full_name = strdup(DNS.domain_name);
	DNS.full_name = sts (&(DNS.full_name), ".");
	DNS.full_name = sts (&(DNS.full_name), DNS.domain_zone);

	DNS.soa = DNS.domain_zone;

	DNS.email = strdup(mail);
	DNS.email = sts (&(DNS.email), ".");
	DNS.email = sts (&(DNS.email), DNS.domain_zone);

	DNS.serial = strdup(serial);
	DNS.refresh = strdup(refresh);
	DNS.retry = strdup(retry);
	DNS.expire = strdup(expire);
	DNS.minimum = strdup(minimum);

	if (mx_situation == 1)
		DNS.mx = NULL;
	if (mx_situation == 2)
		DNS.mx = strdup(mx);
	if (mx_situation == 3) {
		DNS.mx = strdup("relay");
		DNS.mx = sts (&(DNS.mx), ".");
		DNS.mx = sts (&(DNS.mx), DNS.domain_name);
		DNS.mxip = strdup(mx);
	}

	if (ns_situation == 1) {
		DNS.ns1 = strdup(DNS_DEF.ns1);
		DNS.ns1full = strdup(DNS.ns1);

		DNS.ns2 = strdup(DNS_DEF.ns2);
		DNS.ns2full = strdup(DNS.ns2);
	}
	if (ns_situation == 2) {
		DNS.ns1 = strdup(ns1);
		DNS.ns1full = strdup(DNS.ns1);

		DNS.ns2 = strdup(ns2);
		DNS.ns2full = strdup(DNS.ns2);
	}
	if (ns_situation == 3) {
		if (ns2) {
			DNS.ns1 = strdup("ns1");
			DNS.ns1 = sts (&(DNS.ns1), ".");
			DNS.ns1 = sts (&(DNS.ns1), DNS.domain_name);
			DNS.ns1full = strdup("ns1");
			DNS.ns1full = sts (&(DNS.ns1full), ".");
			DNS.ns1full = sts (&(DNS.ns1full), DNS.full_name);
//			if (ns1ip)
			DNS.ns1ip = strdup(ns1);
		}
		if (ns2) {
			DNS.ns2 = strdup("ns2");
			DNS.ns2 = sts (&(DNS.ns2), ".");
			DNS.ns2 = sts (&(DNS.ns2), DNS.domain_name);
			DNS.ns2full = strdup("ns2");
			DNS.ns2full = sts (&(DNS.ns2full), ".");
			DNS.ns2full = sts (&(DNS.ns2full), DNS.full_name);
//			if (ns2ip)
			DNS.ns2ip = strdup(ns2);
		}
	}

	if (ns_situation == 1)
		add_dns_record_by_1(&DNS, mx_situation);
	if (ns_situation == 2)
		add_dns_record_by_2(&DNS, mx_situation);
	if (ns_situation == 3)
		add_dns_record_by_3(&DNS, mx_situation);

	config_shutdown();

	return RES_TRUE;
};
*/
