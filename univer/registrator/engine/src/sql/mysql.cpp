/***************************************************************************
 *   Copyright (C) 2003, 2004                                              *
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
#include <mysql/mysql.h>

#include "define.h"
#include "message.h"
#include "str.h"
#include "zone.h"
#include "msql.h"

using namespace std;

struct mysql_state sql_state;
struct mysql_socket_state sql_socket;


unsigned int tilda_procced;

unsigned int mysql_socket_init()
{
	char * query = NULL;
	sql_state.row = NULL;
	sql_state.result = NULL;
	sql_state.prepare = FALSE;

	//
	//  Connect to MySQL Server
	//

	mysql_init(&sql_state.mysql);
	message("MySQL client: attempt to connect to MySQL Server...\n");
	sql_state.connection = mysql_real_connect(&sql_state.mysql,
    					sql_socket.host,
					sql_socket.user_name,
					sql_socket.password,
					0,0,0,0);

	if (sql_state.connection == NULL) {
    		message("Error: Can't connect to MySQL Server. Check Your username and password.\n");
		sql_socket.connected = FALSE;
	    	return RES_FALSE;
	}
	else
		message("MySQL client: Done.\n");

	//
	//  Select DataBase
	//

	message("MySQL client: attempt to select DataBase...\n");

	sql_state.state = mysql_select_db(&sql_state.mysql, sql_socket.database);

	if (sql_state.state) {
		message("Error: Can not select database. Make sure what Amber was launched at lease one time.\n");
		if (query)
			safe_free(&query);

		sql_socket.connected = FALSE;
		return RES_FALSE;
	}
	else
		message("MySQL client: Done.\n");

	if (query)
		safe_free(&query);

	sql_socket.connected = TRUE;

	message("MySQL client: connection to MySQL Server established.\n");

	return RES_TRUE;
};

unsigned int mysql_prepare_table(unsigned int number)
{
/*
	char * query = NULL;
	unsigned int num_fields = 0;
	unsigned int count = 0;
	unsigned int result = TRUE;

	//
	//  Check for table in database
	//

	count = special_how_much_words(Z[number]->FieldName, TRUE);

	query = (char*)sts(&query, "SELECT * FROM ");
	query = (char*)sts(&query, Z[number]->DataBaseTable);

	sql_state.state = mysql_query(sql_state.connection, query);
	if (query)
		safe_free(&query);

	sql_state.result = mysql_store_result(sql_state.connection);
	mysql_free_result(sql_state.result);

	if (sql_state.state) {

		message("MySQL error: ");
		message((char *)mysql_error(sql_state.connection));
		message("\nError: Can't find table in DataBase. Make sure what Amber was launched at lease one time.\n");

		sql_state.result = mysql_store_result(sql_state.connection);
		mysql_free_result(sql_state.result);

		sql_state.prepare = FALSE;

		return RES_FALSE;
	}
	// Maybe table exists, but one wrong. Check it
	else {
		query = (char*)sts(&query, "SELECT * FROM ");
		query = (char*)sts(&query, Z[number]->DataBaseTable);

		sql_state.state = mysql_query(sql_state.connection, query);
		if (query)
			safe_free(&query);

		sql_state.result = mysql_store_result(sql_state.connection);
		num_fields = mysql_num_fields(sql_state.result);
		mysql_free_result(sql_state.result);

		if (num_fields != (count + 1)) {
			message("Error: Table already exists, but has wrong count of fields.\n");
			result = FALSE;
		}
	}

	sql_state.prepare = result;

	return result;
*/
};

void mysql_close_table()
{
	sql_state.prepare = FALSE;
}

void mysql_socket_close()
{
	if (sql_socket.connected) {
		mysql_close(sql_state.connection);
		message("MySQL client: connection to MySQL Server closed.\n");
	}
};

unsigned int mysql_make_query(char * query)
{

	sql_state.state = mysql_query(sql_state.connection, query);

	if (sql_state.state)	{
		message("MySQL error: ");
		message((char *)mysql_error(sql_state.connection));
	    	message("\nError: Can't complete query to database\n");
		message("Query : \"");
		message(query);
		message("\"\n");
		return RES_FALSE;
	}

	sql_state.result = mysql_store_result(sql_state.connection);
	mysql_free_result(sql_state.result);

	return RES_TRUE;
};

void mysql_client_shutdown()
{
	while(sql_state.row) {
		while(*(sql_state.row))
			free((*(sql_state.row))++);
	free((sql_state.row)++);
	}

	mysql_socket_close();

	if (sql_socket.host)
		safe_free(&sql_socket.host);
	if (sql_socket.user_name)
		safe_free(&sql_socket.user_name);
	if (sql_socket.password)
		safe_free(&sql_socket.password);
	if (sql_socket.database)
		safe_free(&sql_socket.database);
};
