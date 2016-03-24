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


/*
 *  Determine log message system current state:
 */
struct mess_state {
/* Indicate file in open state. Uses if CTRL+C will caught at this time */
	unsigned int 	log_open;
/* Indicate Log system is up */
	unsigned int 	log_init;
/* Log file descriptor */
	FILE * 	log_desc;
/* Log file name */
	char * 	log_file_name;
};

/*
 *  void message(char * messg,...);
 *
 *  Send message to destination defined in mess_mode enum
 *
 *  __Param:
 *
 *  @messg		message which your want output.
 *
 *  __Description:
 *
 *  If @log_init is not zero then message will be send to @mess_mode.
 *  If @log_init is zero then function will exit, unless program was compiled
 *  with DEBUG definition. In this case, message will be send to stdout.
 *
 *  __Return: nothing
 */
void message(char * messg,...);

/*
 *  void message_init(enum mess_mode set_mode, char * file_name);
 *
 *  Setup the log message system
 *
 *  __Param:
 *
 *  @mess_mode		enum, determine where message should be send
 *  @file_name		name of log file ( if @mess_mode == out_to_file )
 *
 *  __Description:
 *
 *  Setup the environment of the log system
 *
 *  __Return: nothing
 */
void message_init(char * file_name);

/*
 *  void message_close();
 *
 *  Send message to destination defined in @mess_mode enum
 *
 *  __Description:
 *
 *  Close Log file if @log_open = true. It uses if the program caught
 *  CTRL+C signal, but Log file is still opened.
 *  This function calls from @Signal_TERM@ function
 *
 *  __Return: nothing
 *
 */
void message_close();
void message_shutdown();
