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

#include <mysql/mysql.h>

#define CREATE_TABLE 1
#define INSERT_DATA_PREPARE 2

//MySQL_List SQLS;

struct mysql_socket_state {
	char *	host;
	char * 	user_name;
	char * 	password;
	char * 	database;
	unsigned int connected;
};

struct mysql_state {
	char ** 	row;
	MYSQL_RES *	result;
	MYSQL *		connection;
	MYSQL 		mysql;
	unsigned int	state;
	unsigned long	records_added;
	unsigned int	prepare;
};

void mysql_client_shutdown();
void mysql_socket_close();
void mysql_close_table();
unsigned int mysql_socket_init();
unsigned int mysql_make_query(char * query);

