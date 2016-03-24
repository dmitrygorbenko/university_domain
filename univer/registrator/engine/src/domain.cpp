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
#endif

#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <ctype.h>

#include "define.h"
#include "str.h"
#include "param.h"
#include "common.h"
#include "sql.h"
#include "domain.h"

using namespace std;

void create_domain()
{
	char * id_full = NULL;
	char * id_full_copy = NULL;
	char * id = NULL;
	unsigned int i;
	unsigned int len;
	unsigned int len_static;
	unsigned int len_id;
	unsigned int pos;
	unsigned int digit_failed;
/*
	What we need to do :
		1. Create DNS records
			1.1. Update special table with id_domain to register
		2. Update SQL tables
			2.1. Update domain state: his now in "in_registation state"
			2.2. Update domain dates: add registration_start date
			2.3. Update client statictis: increase cash
		3. Send an e-mail to administrator
			3.1. Create e-mail
			3.2. Send e-mail
		4. Print to stdout: "Domain created"
		5. Exit
*/
	id_full = get_value("ID");

	if (!id_full) {
		printf("<center class=error>Не указан номер (сервис-код) домена<center>\n");
		return;
	}
	
/*
	if (become_root() == RES_FALSE) {
		printf("<center class=error>Не удалось повысить права до root - не могу изменять конфигурацию хоста<center>\n");
		return;
	}
*/		
	id_full_copy = id_full;

	printf("<center class=first>Регистрация доменного имени</center>");
	printf("<br>");
	
	printf("<table class=standart>\n");
	printf("<tr><td class=td_head colspan=2>Результат обработки доменов</td></tr>\n");

	pos = 0;
	len_static = strlen(id_full);
	do {
		len = strlen(id_full);
		
		for (i=0; i < len; i++)
			if (id_full[i] == ':')
				break;
		if (i == len)
			break;
		
		pos += i + 1;
		id = (char *)calloc(i, 1);
		strncpy(id, id_full, i);
		
		id_full += (sizeof(char)*i + 1);
		
		len_id = strlen(id);
		digit_failed = FALSE;
		for(i=0; i < len_id; i++)
			if (!isdigit(id[i])) {
				digit_failed = TRUE;
				break;
			}

		if (digit_failed == TRUE)
			break;
		
		create_domain_2(atoi(id));
		safe_free(&id);
	} while ((len_static - pos) > 1);
	
	safe_free(&id_full_copy);
	
	printf("</table>\n");
	
//	become_simple();
}

void create_domain_2(unsigned int id)
{
	char * zone_file_name = NULL;
	char * query = NULL;
	char * time = NULL;
	char * cash_count = NULL;
	unsigned int result = RES_FALSE;
	struct __p_answer * domain = NULL;

	domain = parse_result("view_query_body_individ", id);

	if (domain->result_code == SQL_RESULT_ERROR) {
		printf("<tr><td class=td_data_l colspan=2>Caught Error: %s<br>Query: %s</td></tr>\n", domain->errmsg, domain->source_query);
		return;
	}
	if (domain->result_code == SQL_RESULT_EMPTY) {
		printf("<tr><td class=td_data_l colspan=2>class=error>В таблице нет данных (информация о домене)</td></tr>\n");
		return;
	}

	if (domain->domain_status != domain->enum_wait_for_pay) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> не в очереди на оплату - пропускаю</td></tr>\n", domain->d_name, domain->z_name);
		return;
	}

	query = strdup("INSERT INTO to_create (id_domain_data) VALUES ('");
	query = sts(&query, int_to_string(domain->id_domain));
	query = sts(&query, "')");
	result = sql_make_query(query);
//	result = SQL_RESULT_ERROR;
	if (result == SQL_RESULT_ERROR) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> ошибка при выполнении SQL запроса - пропускаю<br>Запрос: %s</td></tr>\n", domain->d_name, domain->z_name, query);
		safe_free(&query);
		return;
	}
	safe_free(&query);

	query = strdup("DELETE FROM wait_for_pay WHERE id_domain_data = '");
	query = sts(&query, int_to_string(domain->id_domain));
	query = sts(&query, "'");
	result = sql_make_query(query);
//	result = SQL_RESULT_ERROR;
	if (result == SQL_RESULT_ERROR) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> ошибка при выполнении SQL запроса - пропускаю<br>Запрос: %s</td></tr>\n", domain->d_name, domain->z_name, query);
		safe_free(&query);
		return;
	}
	safe_free(&query);

	query = strdup("UPDATE domain_status_table SET wait_for_pay = '0', queued = '1' WHERE id_domain_data = '");
	query = sts(&query, int_to_string(domain->id_domain));
	query = sts(&query, "'");
	result = sql_make_query(query);
//	result = SQL_RESULT_ERROR;
	if (result == SQL_RESULT_ERROR) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> ошибка при выполнении SQL запроса - пропускаю<br>Запрос: %s</td></tr>\n", domain->d_name, domain->z_name, query);
		safe_free(&query);
		return;
	}
	safe_free(&query);

	query = strdup("UPDATE dates_table SET payed = '");
	time = get_time_2();
	query = sts(&query, time);
	safe_free(&time);
	query = sts(&query, "' WHERE id_domain_data = '");
	query = sts(&query, int_to_string(domain->id_domain));
	query = sts(&query, "'");
	result = sql_make_query(query);
//	result = SQL_RESULT_ERROR;
	if (result == SQL_RESULT_ERROR) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> ошибка при выполнении SQL запроса - пропускаю<br>Запрос: %s</td></tr>\n", domain->d_name, domain->z_name, query);
		safe_free(&query);
		return;
	}
	safe_free(&query);

	query = strdup("UPDATE person_table SET cash = '");
	cash_count = get_price_for_domain(domain->z_name, domain->lease_time);
	
	if (!cash_count) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> не могу считать стоимость домена - проверьте таблицу domain_cfg</td></tr>\n", domain->d_name, domain->z_name);
		safe_free(&query);
		return;
	}
	query = sts(&query, cash_count);
	safe_free(&cash_count);
	query = sts(&query, "' WHERE id_table IN (SELECT DISTINCT id_person_data FROM person_owns_table WHERE id_domain_data = '");
	query = sts(&query, int_to_string(domain->id_domain));
	query = sts(&query, "')");
	result = sql_make_query(query);
//	result = SQL_RESULT_ERROR;
	if (result == SQL_RESULT_ERROR) {
		printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_red> ошибка при выполнении SQL запроса - пропускаю<br>Запрос: %s</td></tr>\n", domain->d_name, domain->z_name, query);
		safe_free(&query);
		return;
	}
	safe_free(&query);

	printf("<tr><td class=td_data_l>%s.%s</td><td class=td_data_l_green> поставлен на регистрацию</td></tr>\n", domain->d_name, domain->z_name);

	return;
};
