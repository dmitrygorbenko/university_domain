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

#ifdef PGSQL_USE
	#include "pgsql.h"       /* The local header */
#endif

#ifdef MYSQL_USE
	#include "mysql.h"       /* The local header */
#endif

#include "cstdio"
#include "cstdlib"
#include "cstring"
#include "unistd.h"

#include "define.h"
#include "str.h"
#include "sql.h"

using namespace std;

#ifdef PGSQL_USE
	extern struct __pgsql_connection pg_conn;
#endif

#ifdef MYSQL_USE
#endif

unsigned int sql_socket_init() 
{
#ifdef PGSQL_USE
	return pgsql_connect();
#endif
	
#ifdef MYSQL_USE
	return RES_FALSE;
#endif
};

unsigned int sql_make_query(char * query)
{
	if (!query)
		return SQL_RESULT_ERROR;

#ifdef PGSQL_USE
	return pgsql_make_query(query);
#endif

#ifdef MYSQL_USE
	return SQL_RESULT_ERROR;
#endif

};

void sql_close_connect()
{
#ifdef PGSQL_USE
	pgsql_close_connect();
#endif

#ifdef MYSQL_USE

#endif
};

char * sql_get_value(unsigned int row, unsigned int column)
{
	char * result_value = NULL;

#ifdef PGSQL_USE
	result_value = pgsql_get_value(row, column);
#endif

#ifdef MYSQL_USE
	result_value = mysql_get_value(row, column);
#endif

	return result_value;
}

unsigned int sql_get_rows_count()
{
#ifdef PGSQL_USE
	return pgsql_get_rows_count();
#endif

#ifdef MYSQL_USE
	return mysql_get_rows_count();
#endif
};

unsigned int sql_get_cols_count()
{
#ifdef PGSQL_USE
	return pgsql_get_cols_count();
#endif

#ifdef MYSQL_USE
	return mysql_get_cols_count();
#endif
};

char * sql_get_field_name(unsigned int column)
{
#ifdef PGSQL_USE
	return pgsql_get_field_name(column);
#endif

#ifdef MYSQL_USE
	return mysql_get_field_name(column);
#endif
};

unsigned int sql_get_length(unsigned int row, unsigned int column)
{
#ifdef PGSQL_USE
	return pgsql_get_length(row, column);
#endif

#ifdef MYSQL_USE
	return mysql_get_length(row, column);
#endif
};

unsigned int sql_get_is_null(unsigned int row, unsigned int column)
{
#ifdef PGSQL_USE
	return pgsql_get_is_null(row, column);
#endif

#ifdef MYSQL_USE
	return mysql_get_is_null(row, column);
#endif
};


struct __p_answer * parse_result(char * q_type, unsigned int id_domain)
{
	struct __p_answer * pans = NULL;
	char * query = NULL;
	unsigned int result = RES_FALSE;
	unsigned int i = 0;
	
	if (!q_type)
		return NULL;

	pans = new struct __p_answer;
	pans->next = NULL;

	if (strcmp("view_query_body_individ", q_type) == 0) {
		char * id = NULL;

//		id = get_value("ID");
		if (id_domain != 0) {
			safe_free(&id);
			id = int_to_string(id_domain);
		}

		query = strdup("SELECT dd.id_table, dd.d_name, dd.d_zone, "
				"st.id_table, st.wait_for_pay, st.queued, st.checked, st.suspended, st.refused, st.recalled, st.ok, st.hold, st.frozen, st.released, "
				"dat.query_post_date, dat.confirm_date, dat.payed, dat.lease_time, dat.end_time, dat.released, dat.refused, dat.holded, dat.frozen, "
				"des.add_info, des.why_released, des.why_refused, des.notes, "
				"tch.ns1, tch.ns2, tch.mx, tch.query_from_host, tch.confirm_from_host "
				"FROM domain_data dd, domain_status_table st, dates_table dat, descr_table des, technical_table tch "
				"WHERE dd.id_table = ");

		query = sts (&query, id);
		query = sts (&query, " AND st.id_domain_data = ");
		query = sts (&query, id);
		query = sts (&query, " AND dat.id_domain_data = ");
		query = sts (&query, id);
		query = sts (&query, " AND des.id_domain_data = ");
		query = sts (&query, id);
		query = sts (&query, " AND tch.id_domain_data = ");
		query = sts (&query, id);
	
		pans->id_domain = atoi(id);
	}
	
	if (strcmp("view_query_body_individ_person", q_type) == 0) {
		char * id = NULL;

//		id = get_value("ID");

		query = strdup("SELECT * "
				"FROM person_table WHERE id_table IN (SELECT id_person_data FROM person_owns_table WHERE id_domain_data = ");
		query = sts (&query, id);
		query = sts (&query, " )");
	
		pans->id_domain = atoi(id);
	}

//***********************************************************//
//***********************************************************//
//***********************************************************//
	
	pans->source_query = strdup(query);
	result = sql_make_query(query);
	pans->result_code = result;
	safe_free(&query);
	
	if (result == SQL_RESULT_ERROR) {
#ifdef PGSQL_USE
		pans->errmsg = pg_conn.errmsg;
#endif
#ifdef MYSQL_USE

#endif
		return pans;
	}

//***********************************************************//
//***********************************************************//
//***********************************************************//
	
	if (strcmp("view_query_body_individ", q_type) == 0) {
		char * tmp = NULL;
		
		if (pans->result_code == SQL_RESULT_EMPTY)
			return pans;
		
		pans->d_name = sql_get_value(0, 1);
		pans->z_name = sql_get_value(0, 2);

		tmp = sql_get_value(0, 4);
		if (strcmp(tmp, "1")==0) { 
			pans->status = strdup("Ожидает оплаты"); 
			pans->domain_status = pans->enum_wait_for_pay; } safe_free(&tmp);

		tmp = sql_get_value(0, 5);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("В очереди на регистрацию");
			pans->domain_status = pans->enum_queued; } safe_free(&tmp);

		tmp = sql_get_value(0, 6);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Проходит проверку"); 
			pans->domain_status = pans->enum_checked; } safe_free(&tmp);

		tmp = sql_get_value(0, 7);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Обработку отложено"); 
			pans->domain_status = pans->enum_suspended; } safe_free(&tmp);

		tmp = sql_get_value(0, 8);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Отклонен"); 
			pans->domain_status = pans->enum_refused; } safe_free(&tmp);

		tmp = sql_get_value(0, 9);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Заявка отозвана"); 
			pans->domain_status = pans->enum_recalled; } safe_free(&tmp);
		
		tmp = sql_get_value(0, 10);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Активный"); 
			pans->domain_status = pans->enum_ok; } safe_free(&tmp);
		
		tmp = sql_get_value(0, 11);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Приостановлен"); 
			pans->domain_status = pans->enum_hold; } safe_free(&tmp);
		
		tmp = sql_get_value(0, 12);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Заморожен"); 
			pans->domain_status = pans->enum_frozen; } safe_free(&tmp);
		
		tmp = sql_get_value(0, 13);
		if (strcmp(tmp, "1")==0) {
			pans->status = strdup("Свободен"); 
			pans->domain_status = pans->enum_released; } safe_free(&tmp);
		
		pans->query_post_date = sql_get_value(0, 14);
		pans->confirm_date = sql_get_value(0, 15);
		pans->payed = sql_get_value(0, 16);
		pans->lease_time = atoi(sql_get_value(0, 17));
		pans->end_time = sql_get_value(0, 18);
		pans->released = sql_get_value(0, 19);
		pans->refused = sql_get_value(0, 20);
		pans->holded = sql_get_value(0, 21);
		pans->frozen = sql_get_value(0, 22);
		
		pans->add_info = sql_get_value(0, 23);
		pans->why_released = sql_get_value(0, 24);
		pans->why_refused = sql_get_value(0, 25);
		pans->notes = sql_get_value(0, 26);
		
		pans->ns1 = sql_get_value(0, 27);
		pans->ns2 = sql_get_value(0, 28);
		pans->mx = sql_get_value(0, 29);
		pans->query_from_host = sql_get_value(0, 30);
		pans->confirm_from_host = sql_get_value(0, 31);
	}
	
	if (strcmp("view_query_body_individ_person", q_type) == 0) {
		if (pans->result_code == SQL_RESULT_EMPTY)
			return pans;
		
		pans->id_person = atoi(sql_get_value(0, 0));
		pans->firstname = sql_get_value(0, 1);
		pans->lastname = sql_get_value(0, 2);
		pans->company = sql_get_value(0, 3);
		pans->country = sql_get_value(0, 4);
		pans->region = sql_get_value(0, 5);
		pans->postal = atoi(sql_get_value(0, 6));
		pans->city = sql_get_value(0, 7);
		pans->address1 = sql_get_value(0, 8);
		pans->address2 = sql_get_value(0, 9);
		pans->phone = sql_get_value(0, 10);
		pans->fax = sql_get_value(0, 11);
		pans->email = sql_get_value(0, 12);
		pans->userhdl = sql_get_value(0, 14);
		pans->cash = atoi(sql_get_value(0, 15));
	}
/*
	if (strcmp("view_registered", q_type) == 0) {
		struct __p_answer * next_pans = NULL;
		struct __p_answer * pans_copy = NULL;
		
		pans_copy = pans;
		
		if (pans_copy->result_code == SQL_RESULT_EMPTY)
			return pans_copy;
		
		for (i=0; i < sql_get_rows_count(); i++) {
			pans_copy->id_domain = atoi(sql_get_value(i, 0));
			pans_copy->d_name = sql_get_value(i, 1);
			pans_copy->z_name = sql_get_value(i, 2);
			pans_copy->payed = sql_get_value(i, 3);
			pans_copy->end_time = sql_get_value(i, 4);

			if (i < (sql_get_rows_count()-1)) {
				next_pans = new struct __p_answer;
				next_pans->next = NULL;
				pans_copy->next = next_pans;
				pans_copy = next_pans;
			}
		}
	}
*/
	return pans;
};
