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
#include "message.h"
#include "str.h"
#include "sql.h"
#include "dns_manage.h"

using namespace std;

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
		printf("></td></tr></form></table><center class=fatal_error>Caught SIGSEGV</center><br>");

	message_close();

	message("Program halted.\n");
	exit(1);
};

int main(int argc, char *argv[])
{

/***********************************************
	BEGIN PROGRAM INIT
***********************************************/

	Set_Signal_Handler();

/***********************************************
	BEGIN MESSAGE INIT
***********************************************/

	/* Now, I think thas this system in not necessary.
		Why ? - becouse all outputs must be printed to stdout,
	*/

/*
	message_init(MESS_LOG_FILE_NAME);
	t = time(0);
	message("\n================================================================================\n");
	message(PROGRAM_NAME); message(": Started at: ");
	message(asctime(localtime(&t)));
	message("\n");
*/

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


/***********************************************
	PROGRAM COMPLETED
***********************************************/

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
