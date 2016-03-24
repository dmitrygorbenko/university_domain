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

#include <ctype.h>
#include <cstring>
#include <cstdio>
#include <cstdlib>
#include <csignal>
#include <ctime>

#include "define.h"
#include "common.h"
#include "message.h"
#include "param.h"
#include "html.h"
#include "domain.h"
#include "str.h"
#include "sql.h"
#include "engine.h"

using namespace std;

extern struct __pgsql_connection pg_conn;
extern Param P;

void Set_Signal_Handler(void)
{
	signal(SIGINT,   Signal_TERM);
	signal(SIGHUP,   Signal_TERM);
	signal(SIGTERM,  Signal_TERM);
	signal(SIGSEGV,  Signal_TERM);
};

void Signal_TERM(int sig)
{
	if (sig == SIGINT)
		message("\nCaught SIGINT. Closing all...\n");
	if (sig == SIGSEGV)
		printf("></td></tr></form></table></td></tr></table></td></tr></table><center class=fatal_error>Caught SIGSEGV</center><br>");

	message_close();

	message("Program halted.\n");
	exit(1);
};

int main(int argc, char *argv[])
{
	char * pRequestMethod = NULL;
	char * todo = NULL;
	char * query = NULL;
	
/***********************************************
	BEGIN PROGRAM INIT
***********************************************/

	Set_Signal_Handler();
	printf("Content-type: text/html\n\n");

/***********************************************
	BEGIN MESSAGE INIT
***********************************************/

	/* Now, I think thas this system in not necessary.
		Why ? - because all outputs must be printed to stdout,
	*/

/*
	message_init(MESS_LOG_FILE_NAME);
	t = time(0);
	message("\n================================================================================\n");
	message(PROGRAM_NAME); message(": Started at: ");
	message(asctime(localtime(&t)));
	message("\n");
*/

	html_print_header();

/***********************************************
	BEGIN SQL INIT
***********************************************/

	if (sql_socket_init() == RES_FALSE) {
		printf("<center class=fatal_error>Can't connect to Database Server</center>");
		message("\nFATAL ERROR: Can't connect to Database Server.\nQuit.\n");
		message_shutdown();
		exit(1);
	}
	
/***********************************************
	INIT PROGRAM COMPLETED

	BEGIN PROGRAM
***********************************************/

	pRequestMethod = getenv("REQUEST_METHOD");
/*
	if (pRequestMethod == NULL) {
		printf("Error: Request_Method == NULL.<br>");
		html_print_end();
		exit(0);
	}
*/
/*
	extern char **environ;
	int i = 0;
	while(environ[i]) {
		printf("<br>Env[%d] = %s<br>\n", i, environ[i]);
		i++;
	}
*/
	Parse_pairs();
/*
	Param_Nod * nd;
	nd = new Param_Nod;
	nd->param = strdup("TODO");
	nd->value = strdup("search_domain_run");

	P.Add(nd);
	nd = new Param_Nod;
	nd->param = strdup("DOMAIN_STATUS");
	nd->value = strdup("all");
	P.Add(nd);
*/
	todo = get_value("TODO");

//	printf("Todo: \"%s\"\n<br>", todo);

	if (todo) {
		if (strcmp(todo, href_view_select) == 0)
			html_print_view_select();
		if (strcmp(todo, href_view_query) == 0)
			html_print_view_queries();
		if (strcmp(todo, href_view_registered) == 0)
			html_print_view_registered_body();
		if (strcmp(todo, href_view_to_create) == 0)
			html_print_view_to_create_body();
		if (strcmp(todo, href_view_refused) == 0)
			html_print_view_refused_body();
		if (strcmp(todo, href_view_domain_individ) == 0)
			html_print_view_query_body_individ();

		if (strcmp(todo, href_view_clients) == 0)
			html_print_view_clients();
		if (strcmp(todo, href_view_client_individ) == 0)
			html_print_view_client_individ();

		if (strcmp(todo, href_search_domain) == 0)
			html_print_search_domain();
		if (strcmp(todo, href_search_domain_run) == 0)
			html_print_search_domain_run();

		if (strcmp(todo, href_search_client) == 0)
			html_print_search_client();
		if (strcmp(todo, href_search_client_run) == 0)
			html_print_search_client_run();

		if (strcmp(todo, href_config_zone) == 0)
			html_print_config_zone();
		if (strcmp(todo, href_config_zone_view) == 0)
			html_print_config_zone_view();
		if (strcmp(todo, href_config_zone_update) == 0)
			html_print_config_zone_update();

		if (strcmp(todo, href_config_ns_mx) == 0)
			html_print_config_ns_mx();
		if (strcmp(todo, href_config_ns_mx_update) == 0)
			html_print_config_ns_mx_update();
			
		// Создание домена
		if (strcmp(todo, href_accept_query) == 0) {
			create_domain();
		}

		safe_free(&todo);
	}
	else
		html_print_main_body();

/***********************************************
	PROGRAM COMPLETED
***********************************************/

	html_print_end();
	sql_close_connect();

	t = time(0);
	message("\n");
	message("All done at: ");
	message(asctime(localtime(&t)));
	message("Quit.\n");

	message_shutdown();

	exit(0);
	/* silence compiler warnings */
	return EXIT_SUCCESS;
	(void) argc;
	(void) argv;
};
