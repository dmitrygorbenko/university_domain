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
#include <stdarg.h>

#include "define.h"
#include "message.h"
#include "str.h"

using namespace std;

struct mess_state m_state;

void message(char * messg,...)
{
	va_list ap;

	if (m_state.log_init) {
		m_state.log_open = 1;
		m_state.log_desc = fopen(m_state.log_file_name,"a");

		va_start (ap, messg);
		fprintf (m_state.log_desc, messg, ap);
		va_end (ap);

		fclose(m_state.log_desc);
		m_state.log_open = 0;
	}

#if defined (DEBUG) || defined (MESSAGE_DEBUG)
	fprintf(stdout,"DEBUG mode. ");
	if (!m_state.log_init)
		fprintf(stdout,"Log system is disabled. Message: ");
	else
		fprintf(stdout,"Message: ");

		va_start( ap, messg );
		fprintf(m_state.log_desc, messg, ap);
		va_end( ap );
#endif
}

void message_init(char * file_name)
{
	m_state.log_init = 1;
	m_state.log_desc = NULL;

	if(file_name) {
		m_state.log_open = 1;
		m_state.log_file_name = (char *)strdup(file_name);
	        m_state.log_desc = fopen(m_state.log_file_name,"a");
	}
	else {
		fprintf(stdout,"Error: message.cpp: message_init: _BAD_ file_name\n");
		m_state.log_init = 0;
	}

	if (m_state.log_desc == NULL) {
		fprintf(stdout,"Error: message.cpp: message_init: can't open/create log file\n");
		m_state.log_init = 0;
	}
	else
		fclose(m_state.log_desc);

	m_state.log_open = 0;

	if (m_state.log_init == 0) {
		fprintf(stdout,"Error: message.c: message_init: Log system is disabled.\n");
		m_state.log_file_name = NULL;
	}
};

void message_close()
{
	if (m_state.log_open)
		fclose(m_state.log_desc);
};

void message_shutdown()
{
	safe_free(&m_state.log_file_name);
};
