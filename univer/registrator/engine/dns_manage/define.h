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

#define FALSE 0
#define TRUE 1
#define BAD 2
#define RES_FALSE 0
#define RES_TRUE 1
#define RES_BAD 2

#define SQL_RESULT_UNDEFINED 0
#define SQL_RESULT_EMPTY 1
#define SQL_RESULT_TABLE_EMPTY 2
#define SQL_RESULT_ERROR 3
#define SQL_RESULT_ERROR_UNPRINTABLE 4
#define SQL_RESULT_OK 5

#define PGSQL_WAIT_TO_CONNECT 5

#define PROGRAM_NAME "Engine"
#define PROGRAM_VERSION "0.5"

#define NAMED_FILE_NAME "/tmp/domain/named.conf"
#define MESS_LOG_FILE_NAME "/tmp/stat.log"
#define CONFIG_FILE_NAME "/tmp/dns/config"

#define href_main_page			"engine"
#define href_client_main_page		"http://localhost/domain/"
#define href_whois_main_page		"http://whois.com.ua"

#define href_test_page			"test"
#define href_client_pipe		"client_pipe"
#define href_search_select		"search_select"
#define href_view_select		"view_select"

#define href_view_query 		"view_query"
#define href_view_registered 		"view_registered"
#define href_view_refused 		"view_refused"
#define href_view_released 		"view_released"
#define href_view_query_individ 	"view_query_individ"

#define href_search_query 		"search_query"
#define href_search_under_construct 	"search_under_construct"
#define href_search_registered 		"search_registered"
#define href_search_canceled 		"search_canceled"
#define href_search_not_prolonged 	"search_not_prolonged"
#define href_search_released 		"search_released"
#define href_search_query_individ 	"search_query"

#define href_accept_query 		"accept_query"
#define href_prolong_domain 		"prolong"
#define href_reject_query 		"reject_query"
#define href_reject_query_step_2 	"reject_query_step_2"
