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

void Signal_TERM(int sig)
{
	exit(1);
};

void Set_Signal_Handler(void)
{
	signal(SIGINT,   Signal_TERM);
	signal(SIGHUP,   Signal_TERM);
	signal(SIGTERM,  Signal_TERM);
	signal(SIGSEGV,  Signal_TERM);
};

int main(int argc, char *argv[])
{
	printf("Now, you see, I work !\n");
	
	exit(0);
	/* silence compiler warnings */
	return EXIT_SUCCESS;
	(void) argc;
	(void) argv;
};
