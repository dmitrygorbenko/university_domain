/***************************************************************************
 *   Copyright (C) 2006 by Dmitriy Gorbenko                                *
 *   nial@ukr.net                                                          *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   You should have received a copy of the GNU General Public License     *
 *   along with this program; if not, write to the                         *
 *   Free Software Foundation, Inc.,                                       *
 *   59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
 ***************************************************************************/

#undef FALSE
#undef TRUE
#define TRUE 1
#define FALSE 0

#define ROOT_COMPILE 0
#define CHILD_MAX_COUNT 10

#define ID "REChW6uHBBx7BtGz"

#define PROGRAM "ns_control"
#define CONFIG_FILE "/etc/ns_control"
#define LOG_FILE "/var/log/ns_control_log"

#ifdef FREEBSD
	#define RNDC_PATH "/hosting/system/bind/sbin/rndc"
	#define RNDC_COMMAND "reload"
#else
	#define RNDC_PATH "/hosting/system/bind/sbin/rndc"
	#define RNDC_COMMAND "reload"
#endif

