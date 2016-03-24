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
#include <cstring>
#endif

#include <cstdio>
#include <cstdlib>
#include <ctime>

#include "param.h"
#include "html.h"
#include "define.h"
#include "message.h"
#include "str.h"
#include "sql.h"
#include "common.h"

using namespace std;

extern struct __pgsql_connection pg_conn;

void html_print_header()
{
	printf("<HTML>\n"
		"<head>\n"
		"<meta http-equiv=Content-Type content=text/html; charset=koi8-r />\n"
		
		"<style type=text/css>\n"
			".text			{font-family: sans-serif; font-size: 13px; color: #000000;}\n"
			"SPAN.center		{font-family: sans-serif; text-align: center;}\n"
			"A:link			{font-family: sans-serif; font-size: 13px; text-decoration: none; color: #1642A3;}\n"
			"A:visited		{font-family: sans-serif; font-size: 13px; text-decoration: none; color: #1642A3;}\n"
			"A:hover		{font-family: sans-serif; font-size: 13px; text-decoration: underline; color: #FF0000;}\n"
			"A:link.nav		{font-family: sans-serif; font-size: 13px; color: #1642A3;}\n"
			"A:visited.nav		{font-family: sans-serif; font-size: 13px; color: #1642A3;}\n"
			"A:hover.nav		{font-family: sans-serif; font-size: 13px; color: #FF0000;}\n"
			".nav			{font-family: sans-serif; font-size: 13px; color: #000000;}\n"
			".error			{font-family: sans-serif; font-size: 20px; color: #ff0000;}\n"
			".error_simple		{font-family: sans-serif; font-size: 15px; color: #000000;}\n"
			"center.fatal_error  	{font-family: sans-serif; font-size: 20px; color: #ff0000;}\n"
			"center.error		{font-family: sans-serif; font-size: 20px; color: #ff0000;}\n"
			"center.first		{font-family: sans-serif; font-size: 20px; color: #000000;}\n"
			"center.second		{font-family: sans-serif; font-size: 17px; color: #000000;}\n"
			"TABLE.standart		{font-family: sans-serif; font-size: 15px; color: #ffffff; text-align: center; border: solid windowtext 0px; padding: 0px 0px 0px 0px;}\n"
			"TD.td_head		{font-family: sans-serif; font-size: 15px; color: #ffffff; background: #397EBB; text-align: center; padding: 0px 15px 0px 15px;}\n"
			"TD.td_head_l		{font-family: sans-serif; font-size: 15px; color: #ffffff; background: #397EBB; text-align: left; padding: 0px 15px 0px 15px;}\n"
			"TD.td_head_r		{font-family: sans-serif; font-size: 15px; color: #ffffff; background: #397EBB; text-align: right; padding: 0px 15px 0px 15px;}\n"
			"TD.td_head_2		{font-family: sans-serif; font-size: 15px; color: #000000; background: #79C409; text-align: left; padding: 0px 15px 0px 15px;}\n"
			"TD.td_head_2_c		{font-family: sans-serif; font-size: 15px; color: #000000; background: #79C409; text-align: center; padding: 0px 15px 0px 15px;}\n"
			"TD.td_head_2_r		{font-family: sans-serif; font-size: 15px; color: #000000; background: #79C409; text-align: right; padding: 0px 15px 0px 15px;}\n"
			"A.td_head:link		{font-family: sans-serif; font-size: 15px; text-decoration: none; color: #ffffff;}\n"
			"A.td_head:visited	{font-family: sans-serif; font-size: 15px; text-decoration: none; color: #ffffff;}\n"
			"A.td_head:hover	{font-family: sans-serif; font-size: 15px; text-decoration: underline; color: #ffffff;}\n"
			"A.td_data:link		{font-family: sans-serif; font-size: 13px; text-decoration: none; color: #1642A3;}\n"
			"A.td_data:visited	{font-family: sans-serif; font-size: 13px; text-decoration: none; color: #1642A3;}\n"
			"A.td_data:hover	{font-family: sans-serif; font-size: 13px; text-decoration: underline; color: #FF0000;}\n"
			"A.td_tiny_text:link	{font-family: sans-serif; font-size: 10px; text-decoration: none; color: #ffffff;}\n"
			"A.td_tiny_text:visited	{font-family: sans-serif; font-size: 10px; text-decoration: none; color: #ffffff;}\n"
			"A.td_tiny_text:hover	{font-family: sans-serif; font-size: 10px; text-decoration: underline; color: #ffffff;}\n"
			"A.td_tiny_text_dark:link	{font-family: sans-serif; font-size: 10px; text-decoration: none; color: #000000;}\n"
			"A.td_tiny_text_dark:visited	{font-family: sans-serif; font-size: 10px; text-decoration: none; color: #000000;}\n"
			"A.td_tiny_text_dark:hover	{font-family: sans-serif; font-size: 10px; text-decoration: underline; color: #000000;}\n"
			"A.td_normal_text:link		{font-family: sans-serif; font-size: 15px; text-decoration: none; color: #ffffff;}\n"
			"A.td_normal_text:visited	{font-family: sans-serif; font-size: 15px; text-decoration: none; color: #ffffff;}\n"
			"A.td_normal_text:hover		{font-family: sans-serif; font-size: 15px; text-decoration: underline; color: #ffffff;}\n"
			"A.td_normal_text_dark:link	{font-family: sans-serif; font-size: 15px; text-decoration: none; color: #000000;}\n"
			"A.td_normal_text_dark:visited	{font-family: sans-serif; font-size: 15px; text-decoration: none; color: #000000;}\n"
			"A.td_normal_text_dark:hover	{font-family: sans-serif; font-size: 15px; text-decoration: underline; color: #000000;}\n"
			"TD.td_data		{font-family: sans-serif; font-size: 13px; color: #000000; text-align: center;; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_l		{font-family: sans-serif; font-size: 13px; color: #000000; text-align: left; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_l_m		{font-family: sans-serif; font-size: 13px; color: #000000; text-align: left; padding: 0px 10px 0px 10px;}\n"
			"TD.td_data_l_red	{font-family: sans-serif; font-size: 13px; color: #AA0000; text-align: left; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_l_green	{font-family: sans-serif; font-size: 13px; color: #00AA00; text-align: left; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_r		{font-family: sans-serif; font-size: 13px; color: #000000; text-align: right; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_r_m		{font-family: sans-serif; font-size: 13px; color: #000000; text-align: right; padding: 0px 10px 0px 10px;}\n"
			"TD.td_data_r_red	{font-family: sans-serif; font-size: 13px; color: #AA0000; text-align: right; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_r_green	{font-family: sans-serif; font-size: 13px; color: #00AA00; text-align: right; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_c		{font-family: sans-serif; font-size: 13px; color: #000000; text-align: center; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_c_m		{font-family: sans-serif; font-size: 13px; color: #000000; text-align: center; padding: 0px 10px 0px 10px;}\n"
			"TD.td_data_c_red	{font-family: sans-serif; font-size: 13px; color: #AA0000; text-align: center; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data_c_green	{font-family: sans-serif; font-size: 13px; color: #00AA00; text-align: center; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data1		{font-family: sans-serif; font-size: 13px; color: #000000; background: #dfdcdf; text-align: center; padding: 0px 15px 0px 15px;}\n"
			"TD.td_data2		{font-family: sans-serif; font-size: 13px; color: #000000; background: #c7c7c7; text-align: center; padding: 0px 15px 0px 15px;}\n"
			"TR.td_data1		{font-family: sans-serif; font-size: 13px; color: #000000; background: #dfdcdf; text-align: center;}\n"
			"TR.td_data2		{font-family: sans-serif; font-size: 13px; color: #000000; background: #c7c7c7; text-align: center;}\n"
		"//-->\n"
		"</style>\n"
		"   <title></title>\n"
		"</head>\n"
		"<body BGCOLOR=#E9E9E9 text=#000000 >\n"
		"\n"
		"<table width=100% border=0 valign=top>\n"
		"<tr width=100%>\n"
		"	<td width=150px valign=top>\n");
		
	html_print_menu();
		
	printf("</td>\n"
		"\n"
		"	<td width=10px valign=top bgcolor=#000000 ></td>\n"
		"	<td width=10px valign=top ></td>\n"
		"	<td width=99% valign=top>\n");
};

void html_print_menu()
{
	printf(
		"\n"
		"<table width=100% valign=top border=0>\n"
		"	<tr><td class=td_head_2>����</td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s>�������</a></td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s>���� �����������</a></td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s>Whois ����</a></td></tr>\n"
		"	<tr><td class=td_head_2>������</td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>������������</a></td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>�� �����������</a></td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>�����������������</a></td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>�������������</a></td></tr>\n"
		"	<tr><td class=td_head_2>�������</td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>������ ��������</a></td></tr>\n"
		"	<tr><td class=td_head_2>�����</td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>�������</a></td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>��������</a></td></tr>\n"
		"	<tr><td class=td_head_2>���������</td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>�������� ����</a></td></tr>\n"
		"	<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=%s?todo=%s>NS � MX �������</a></td></tr>\n"
		"</table>",
		href_main_page,
		href_client_main_page,
		href_whois_main_page,
		href_main_page, href_view_query,
		href_main_page, href_view_to_create,
		href_main_page, href_view_registered,
		href_main_page, href_view_select,

		href_main_page, href_view_clients,
		
		href_main_page, href_search_domain,
		href_main_page, href_search_client,

		href_main_page, href_config_zone,
		href_main_page, href_config_ns_mx );
}

void html_print_end()
{
	printf("</td></tr></table></body>\n</html>\n");
};

void html_print_main_body()
{
	struct __p_answer * for_pay = NULL;
	struct __p_answer * registered = NULL;
	struct __p_answer * to_create = NULL;
	struct __p_answer * refused = NULL;
	
	printf("<center class=first>����������� �������� ����</center>");
	printf("<br>");

	for_pay = parse_result("count_for_main_page_wait_for_pay");

	if (for_pay->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", for_pay->errmsg, for_pay->source_query);
		return;
	}
	if (for_pay->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ������� wait_for_pay ��� ������</center>");
		return;
	}

	registered = parse_result("count_for_main_page_registered");

	if (registered->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", registered->errmsg, registered->source_query);
		return;
	}
	if (registered->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ������� registered ��� ������</center>");
		return;
	}

	to_create = parse_result("count_for_main_page_to_create");

	if (to_create->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", to_create->errmsg, to_create->source_query);
		return;
	}
	if (to_create->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ������� to_create ��� ������</center>");
		return;
	}

	refused = parse_result("count_for_main_page_refused");

	if (refused->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", refused->errmsg, refused->source_query);
		return;
	}
	if (refused->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ������� refused ��� ������</center>");
		return;
	}

	printf("<table class=standart>\n");
	
	printf("<tr><td class=td_head colspan=3>�� ������ ������:</td></tr>\n");

	printf("<tr><td class=td_data_l_m>��������� ������</td><td class=td_data_l>%d</td>", for_pay->in_query_to_pay);
	if (for_pay->in_query_to_pay > 0)
		printf("<td class=td_data_l_m><a href=%s?todo=%s>��������</a></td></tr>", href_main_page, href_view_query);
	
	printf("<tr><td class=td_data_l_m>�����������������</td><td class=td_data_l>%d", registered->in_registered);
	if (registered->in_registered > 0)
		printf("<td class=td_data_l_m><a href=%s?todo=%s>��������</a></td></tr>", href_main_page, href_view_registered);
	
	printf("<tr><td class=td_data_l_m>�� �����������</td><td class=td_data_l>%d", to_create->in_to_create);
	if (to_create->in_to_create > 0)
		printf("<td class=td_data_l_m><a href=%s?todo=%s>��������</a></td></tr>", href_main_page, href_view_to_create);

	printf("<tr><td class=td_data_l_m>��������</td><td class=td_data_l>%d", refused->in_refused);
	if (refused->in_refused > 0)
		printf("<td class=td_data_l_m><a href=%s?todo=%s>��������</a></td></tr>", href_main_page, href_view_refused);
	
	printf("</table>\n");
};

void html_print_view_select()
{
	printf("<center class=first>������: �������������</center>");
	printf("<br>");
	printf("<table class=standart>\n");
	printf("<tr>\n");
	printf("<td class=td_head >���������</td>\n");
	printf("<td class=td_head >��������</td>\n");
	printf("</tr>\n");
	
	printf("\n"
		"	<tr><td class=td_data_l_m><a href=%s?todo=%s>������������</a></td><td class=td_data_l_m>����������� ������ �� �������������</td></tr>\n"
		"	<tr><td class=td_data_l_m><a href=%s?todo=%s>�� �����������</a></td><td class=td_data_l_m>������, ������������ �� �����������</td></tr>\n"
		"	<tr><td class=td_data_l_m><a href=%s?todo=%s>������������������</a></td><td class=td_data_l_m>������������������ ������</td></tr>\n"
		"	<tr><td class=td_data_l_m><a href=%s?todo=%s>�� ���������</a></td><td class=td_data_l_m>������, �������� ������ �� ���������</td></tr>\n"
		"	<tr><td class=td_data_l_m><a href=%s?todo=%s>�� ��������</a></td><td class=td_data_l_m>������, ������������ ����� ����� �������������</td></tr>\n"
		"	<tr><td class=td_data_l_m><a href=%s?todo=%s>���������</a></td><td class=td_data_l_m>������, � ������������� ������� ��������</td></tr>\n"
		"</table>",
		href_main_page, href_view_query,
		href_main_page, href_view_to_create,
		href_main_page, href_view_registered,
		href_main_page, href_view_to_prolong,
		href_main_page, href_view_to_delete,
		href_main_page, href_view_refused );
};

void html_print_view_queries()
{
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;
	
	printf("\n<script language=javascript>\n");
	printf("	function create_domain(domain_count) {\n");
	printf("		var id = \"&id=\";\n");
	printf("		var empty = \"true\";\n");
	printf("		form = document.getElementById(\"create_domain_form\");\n");
	printf("		for (var i=0; i < domain_count; i++) {\n");
	printf("			check = document.getElementById(\"checkbox_\" + i);\n");
	printf("			if (check.checked == true) {\n");
	printf("				id = id + check.name + \":\";\n");
	printf("				empty = \"false\";\n");
	printf("			}\n");
	printf("		}\n");
	printf("		\n");
	printf("		if (empty == \"true\") {\n");
	printf("			alert(\"�� ������ ������� �����(�)\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		url = \"%s?todo=%s\";\n", href_main_page, href_accept_query);
	printf("		url = url + id;\n");
	printf("		form.action = url;\n");
	printf("		form.submit();\n");
	printf("	}\n");
	printf("</script>\n\n");
	
	printf("\n<center class=first>������, ��������� ������</center>\n");

	panswer = parse_result("view_queries");

	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>������ �� �����������</center>");
		return;
	}

	printf("<br>\n");
	printf("<form method=POST name=\"create_domain_form\" id=\"create_domain_form\">\n");
	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 

	printf("<tr>\n");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_query, sql_get_field_name(0), "������-���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_query, sql_get_field_name(1), "�������� ���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_query, sql_get_field_name(2), "����");
	printf("<td class=td_head >����� �������</td>\n");
	printf("</tr>\n");

	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");

		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->id_domain);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->d_name);
		printf("<td class=td_data >%s</td>\n", panswer->z_name);
		printf("<td class=td_data ><input type=\"checkbox\" name=\"%d\" id=\"checkbox_%d\"></td>\n", panswer->id_domain, i);
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("<tr height=44px> <td></td> <td></td> <td></td>\n"\
		"<td><input type=button value=\"������� �� ����������\" onClick=\"create_domain(%d)\"></td></tr>\n"\
		"</table>\n", i);
	printf("</from>\n");
};

void html_print_view_registered_body()
{
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;

	printf("\n<center class=first>������������������ ������</center>\n");

	panswer = parse_result("view_registered");

	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>������������������ ������� ���</center>");
		return;
	}

	printf("<br>\n");
	printf("<form action=%s method=POST>\n", href_accept_query);
	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 
	
	printf("<tr>\n");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_registered, sql_get_field_name(0), "������-���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_registered, sql_get_field_name(1), "�������� ���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_registered, sql_get_field_name(2), "����");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_registered, sql_get_field_name(3), "������ �������������");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_registered, sql_get_field_name(4), "����� �������������");
	printf("</tr>\n");
	
	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
	
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->id_domain);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->d_name);
		printf("<td class=td_data >%s</td>\n", panswer->z_name);
		printf("<td class=td_data >%s</td>\n", panswer->payed);
		printf("<td class=td_data >%s</td>\n", panswer->end_time);
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("</table>\n");
	printf("</from>\n");
};

void html_print_view_to_create_body()
{
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;

	printf("\n<center class=first>�� ������ �����������</center>\n");

	panswer = parse_result("view_to_create");

	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>������� �� ������ ���������� ���</center>");
		return;
	}

	printf("<br>\n");
	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 
	
	printf("<tr>\n");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_to_create, sql_get_field_name(0), "������-���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_to_create, sql_get_field_name(1), "�������� ���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_to_create, sql_get_field_name(2), "����");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_to_create, sql_get_field_name(3), "���� ������");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_to_create, sql_get_field_name(4), "���� �������������");
	printf("</tr>\n");
	
	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
	
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->id_domain);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->d_name);
		printf("<td class=td_data >%s</td>\n", panswer->z_name);
		printf("<td class=td_data >%s</td>\n", panswer->payed);
		printf("<td class=td_data >%d ���(�)</td>\n", panswer->lease_time);
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("</table>\n");
};

void html_print_view_refused_body()
{
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;

	printf("\n<center class=first>������, ������� �������� � �����������</center>\n");

	panswer = parse_result("view_refused");

	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>������, ������� �������� � �����������, �����������</center>");
		return;
	}

	printf("<br>\n");
	printf("<form action=%s method=POST>\n", href_accept_query);
	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 
	
	printf("<tr>\n");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_refused, sql_get_field_name(0), "������-���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_refused, sql_get_field_name(1), "�������� ���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_refused, sql_get_field_name(2), "����");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_refused, sql_get_field_name(3), "���� ������");
	printf("</tr>\n");
	
	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
	
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->id_domain);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->d_name);
		printf("<td class=td_data >%s</td>\n", panswer->z_name);
		printf("<td class=td_data >%s</td>\n", panswer->refused);
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("</table>\n");
	printf("</from>\n");
};

void html_print_view_clients()
{
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;

	printf("\n<center class=first>�������</center>\n");

	panswer = parse_result("view_clients");

	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>������������������ �������� ���</center>");
		return;
	}

	printf("<br>\n");
	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 
	
	printf("<tr>\n");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_clients, sql_get_field_name(0), "�����");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_clients, sql_get_field_name(1), "���");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_clients, sql_get_field_name(2), "�������");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_clients, sql_get_field_name(3), "E-mail");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_clients, sql_get_field_name(4), "NIC-handle");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_clients, sql_get_field_name(5), "�������");
	printf("<td class=td_head ><a class=td_head href=%s?todo=%s&sort=%s>%s</a></td>\n", 
		href_main_page, href_view_clients, sql_get_field_name(6), "�������");
	printf("</tr>\n");

	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
	
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->id_person);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->firstname);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->lastname);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->email);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->userhdl);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s ���.</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->cash);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->domain_registered);
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("</table>\n");
};

void html_print_view_client_individ()
{
	char * id = NULL;
	unsigned int i = 0;
	struct __p_answer * panswer_person = NULL;
	struct __p_answer * panswer_person_count = NULL;
	struct __p_answer * panswer_domain_list = NULL;
	struct __p_answer * panswer = NULL;

	id = get_value("ID");

	if (!id) {
		printf("<center class=error>�� ������ ����� (���������� �����) �������<center>\n");
		return;
	}
	safe_free(&id);
	
	printf("<center class=first>��������� ���������� � �������</center>\n");
	
	panswer_person = parse_result("view_client_individ");

	if (panswer_person->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span>\n", panswer_person->errmsg, panswer_person->source_query);
		return;
	}
	if (panswer_person->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ���� ������ ��� ���������� � ����� �������</center>\n");
		return;
	}

	panswer_person_count = parse_result("how_many_person_registered_2");

	if (panswer_person_count->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span>\n", panswer_person_count->errmsg, panswer_person_count->source_query);
		return;
	}
	if (panswer_person_count->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ���� ������ ��� ���������� � ������� �������</center>\n");
		return;
	}

	panswer_domain_list = parse_result("domain_list");

	if (panswer_domain_list->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span>\n", panswer_domain_list->errmsg, panswer_domain_list->source_query);
		return;
	}
	if (panswer_domain_list->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ���� ������ ��� ���������� � ������� �������</center>\n");
		return;
	}

		
	printf("<br>");
	
	printf("<table class=standart cellspacing=10 cellpadding=0 >\n");
	printf("<tr><td valign=top>\n");

	panswer = panswer_person;

	printf("<table class=standart cellspacing=0 cellpadding=0 >\n"); 
	printf("<tr><td class=td_head colspan=2 height=20px>������</td></tr>\n");
	printf("<tr><td class=td_data width=150px></td><td width=100px></td></tr>\n");
	
	printf("<tr><td class=td_data_l >���������� �����</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", panswer->id_person);
	printf("<tr><td class=td_data_l >���</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->firstname);
	printf("<tr><td class=td_data_l >�������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->lastname);
	printf("<tr><td class=td_data_l >��������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->company);
	printf("<tr><td class=td_data_l >������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->country);
	printf("<tr><td class=td_data_l >�������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->region);
	printf("<tr><td class=td_data_l >�������� ������</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", panswer->postal);
	printf("<tr><td class=td_data_l >�����</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->city);
	printf("<tr><td class=td_data_l >�����</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->address1);
	printf("<tr><td class=td_data_l >����� 2</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->address2);
	printf("<tr><td class=td_data_l >�������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->phone);
	printf("<tr><td class=td_data_l >��������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->fax);
	printf("<tr><td class=td_data_l >E-mail</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->email);
	printf("<tr><td class=td_data_l >NIC-handle</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->userhdl);
	printf("</table>\n"); 
	
	printf("<p>\n");

	printf("<table class=standart cellspacing=0 cellpadding=0 >\n"); 
	printf("<tr><td class=td_head colspan=2 height=20px>���������� �������</td></tr>\n");
	printf("<tr><td class=td_data width=150px></td><td width=100px></td></tr>\n");

	printf("<tr><td class=td_data_l >�������� �����</td>");
	printf("<td class=td_data_l >%s ���.</td></tr>\n", panswer->cash);
	
	panswer = panswer_person_count;
	
	printf("<tr><td class=td_data_l >���������� �������</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", panswer->domain_registered);
	printf("</table>\n"); 

	printf("</td>\n");
	// First Column END
	
	// Second Column First Row BEGIN
	printf("<td valign=top>\n");

	panswer = panswer_domain_list;

	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 
	
	printf("<tr>\n");
	printf("<td class=td_head >������ �������</td>\n");
	printf("</tr>\n");
	
	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
	
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->full_name);
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("</table>\n");	
	
	printf("</td></tr>\n");
	// Second Column END
	
	// Third Row BEGIN
	printf("<td valign=top colspan=2>\n");

	printf("</td></tr>\n");
	printf("</table>\n");
	// Second Column END

};


void html_print_view_query_body_individ()
{
	char * id = NULL;
	char * full_dates = NULL;
	char * full_descr = NULL;
	char * addit_da = NULL;
	char * addit_de = NULL;
	unsigned int view_full_dates = FALSE;
	unsigned int view_full_descr = FALSE;
	struct __p_answer * panswer_domain = NULL;
	struct __p_answer * panswer_person = NULL;
	struct __p_answer * panswer_person_count = NULL;
	struct __p_answer * panswer = NULL;

	id = get_value("ID");
	full_dates = get_value("FD");
	full_descr = get_value("FDESCR");

	if (!id) {
		printf("<center class=error>�� ������ ����� (������-���) ������<center>\n");
		return;
	}
	safe_free(&id);
	
	if (full_dates) {
		view_full_dates = TRUE;
		addit_da = sts (&addit_da, "&fd=on");
		safe_free(&full_dates);
	}

	if (full_descr) {
		view_full_descr = TRUE;
		addit_de = sts (&addit_de, "&fdescr=on");
		safe_free(&full_descr);
	}
	
	printf("<center class=first>��������� ���������� � ������</center>\n");
	
	panswer_domain = parse_result("view_query_body_individ");

	if (panswer_domain->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span>\n", panswer_domain->errmsg, panswer_domain->source_query);
		return;
	}
	if (panswer_domain->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ���� ������ ��� ���������� � ����� ������</center>\n");
		return;
	}

	panswer_person = parse_result("view_query_body_individ_person");

	if (panswer_person->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span>\n", panswer_person->errmsg, panswer_person->source_query);
		return;
	}
	if (panswer_person->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ���� ������ ��� ���������� � ������� ����� ������</center>\n");
		return;
	}

	panswer_person_count = parse_result("how_many_person_registered");

	if (panswer_person_count->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span>\n", panswer_person_count->errmsg, panswer_person_count->source_query);
		return;
	}
	if (panswer_person_count->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>� ���� ������ ��� ���������� � ������� ����� ������</center>\n");
		return;
	}
	
	printf("<br>");
	
	printf("<table class=standart cellspacing=10 cellpadding=0 >\n");
	// First Column First Row BEGIN
	printf("<tr><td valign=top>\n");
	
	panswer = panswer_domain;
	
	printf("<table class=standart cellspacing=0 cellpadding=0>\n"); 
	printf("<tr><td class=td_head colspan=2 height=20px>��������</td></tr>\n");
	printf("<tr><td class=td_data width=150px></td><td width=185px></td></tr>\n");
	
	printf("<tr><td class=td_data_l >������ ���</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", panswer->id_domain);
	printf("<tr><td class=td_data_l >�������� ���</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->d_name);
	printf("<tr><td class=td_data_l >�������� ����</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->z_name);
	printf("<tr><td class=td_data_l >��������� ������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->status);
	printf("</table>\n"); 

	// First Column Second Row BEGIN
	printf("<p>\n");
	
	printf("<table class=standart cellspacing=0 cellpadding=0 >\n"); 
	if (view_full_dates == FALSE)
		printf("<tr><td class=td_head colspan=2 height=20px>���� (<a class=td_tiny_text href=%s?todo=%s&id=%d&fd=on%s >��� ����</a>)</td></tr>\n", href_main_page, href_view_domain_individ, panswer->id_domain, (addit_de?addit_de:""));
	else
		printf("<tr><td class=td_head colspan=2 height=20px>���� (<a class=td_tiny_text href=%s?todo=%s&id=%d%s >������</a>)</td></tr>\n", href_main_page, href_view_domain_individ, panswer->id_domain, (addit_de?addit_de:""));
	printf("<tr><td class=td_data width=160px></td><td width=100px></td></tr>\n");

	if (view_full_dates == TRUE)
		goto view_full_dates_label;
	
	if (panswer->domain_status == panswer->enum_wait_for_pay) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_date);
	}
	
	if (panswer->domain_status == panswer->enum_queued) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_date);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
	}

	if (panswer->domain_status == panswer->enum_checked) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_date);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
	}

	if (panswer->domain_status == panswer->enum_suspended) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_date);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
	}

	if (panswer->domain_status == panswer->enum_refused) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_date);
		printf("<tr><td class=td_data_l >������ ���������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->refused);
	}
	
	if (panswer->domain_status == panswer->enum_recalled) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_date);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
	}

	if (panswer->domain_status == panswer->enum_ok) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
		printf("<tr><td class=td_data_l >����� �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->end_time);
	}
	
	if (panswer->domain_status == panswer->enum_hold) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
		printf("<tr><td class=td_data_l >����� �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->end_time);
		printf("<tr><td class=td_data_l >�������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->holded);
	}

	if (panswer->domain_status == panswer->enum_frozen) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
		printf("<tr><td class=td_data_l >����� �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->end_time);
		printf("<tr><td class=td_data_l >������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->frozen);
	}

	if (panswer->domain_status == panswer->enum_released) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
		printf("<tr><td class=td_data_l >����� �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->end_time);
		printf("<tr><td class=td_data_l >����������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->released);
	}

view_full_dates_label:
	if (view_full_dates == TRUE) {
		printf("<tr><td class=td_data_l >���� �����������</td>");
		printf("<td class=td_data_l >%d ���(�)</td></tr>\n", panswer->lease_time);
		printf("<tr><td class=td_data_l >������ ������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_post_date);
		printf("<tr><td class=td_data_l >������ �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_date);
		printf("<tr><td class=td_data_l >������ ��������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->payed);
		printf("<tr><td class=td_data_l >����� �������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->end_time);
		printf("<tr><td class=td_data_l >������ ���������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->refused);
		printf("<tr><td class=td_data_l >�������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->holded);
		printf("<tr><td class=td_data_l >������������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->frozen);
		printf("<tr><td class=td_data_l >����������</td>");
		printf("<td class=td_data_l >%s</td></tr>\n", panswer->released);
	}
	// </table> of Dates
	printf("</table>\n"); 

	// First Column Third Row BEGIN
	printf("<p>\n");
	
	printf("<table class=standart cellspacing=0 cellpadding=0 >\n"); 
	printf("<tr><td class=td_head colspan=2 height=20px>�����������</td></tr>\n");
	printf("<tr><td class=td_data width=165px></td><td width=165px></td></tr>\n");
	
	printf("<tr><td class=td_data_l >NameServer 1</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->ns1);
	printf("<tr><td class=td_data_l >NameServer 2</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->ns2);
	printf("<tr><td class=td_data_l >MailExchange</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->mx);
	printf("<tr><td class=td_data_l >������ � ������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->query_from_host);
	printf("<tr><td class=td_data_l >������������ � ������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->confirm_from_host);
	printf("</table>\n"); 
	
	printf("</td>\n");
	// First Column END
	
	// Second Column First Row BEGIN
	printf("<td valign=top>\n");
	
	panswer = panswer_person;

	printf("<table class=standart cellspacing=0 cellpadding=0 >\n"); 
	printf("<tr><td class=td_head colspan=2 height=20px> <a class=td_normal_text href=%s?todo=%s&id=%d>������</a></td></tr>\n", href_main_page, href_view_client_individ, panswer->id_person);
	printf("<tr><td class=td_data width=150px></td><td width=130px></td></tr>\n");
	
	printf("<tr><td class=td_data_l >���������� �����</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", panswer->id_person);
	printf("<tr><td class=td_data_l >���</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->firstname);
	printf("<tr><td class=td_data_l >�������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->lastname);
	printf("<tr><td class=td_data_l >��������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->company);
	printf("<tr><td class=td_data_l >������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->country);
	printf("<tr><td class=td_data_l >�������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->region);
	printf("<tr><td class=td_data_l >�������� ������</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", panswer->postal);
	printf("<tr><td class=td_data_l >�����</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->city);
	printf("<tr><td class=td_data_l >�����</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->address1);
	printf("<tr><td class=td_data_l >����� 2</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->address2);
	printf("<tr><td class=td_data_l >�������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->phone);
	printf("<tr><td class=td_data_l >��������</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->fax);
	printf("<tr><td class=td_data_l >E-mail</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->email);
	printf("<tr><td class=td_data_l >NIC-handle</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", panswer->userhdl);
//	domain_registered
	printf("</table>\n"); 

	printf("<p>\n");
	
	printf("<table class=standart cellspacing=0 cellpadding=0 >\n"); 
	printf("<tr><td class=td_head colspan=2 height=20px>���������� �������</td></tr>\n");
	printf("<tr><td class=td_data width=150px></td><td width=130px></td></tr>\n");

	printf("<tr><td class=td_data_l >�������� �����</td>");
	printf("<td class=td_data_l >%s ���.</td></tr>\n", panswer->cash);
	
	panswer = panswer_person_count;
	
	printf("<tr><td class=td_data_l >���������� �������</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", panswer->domain_registered);
	printf("</table>\n"); 


	printf("</td></tr>\n");
	// Second Column END
	
	// Third Row BEGIN
	printf("<td valign=top colspan=2>\n");
	
	panswer = panswer_domain;
	
	printf("<table class=standart cellspacing=0 cellpadding=0 width=100% >\n"); 
	if (view_full_descr == FALSE)
		printf("<tr><td class=td_head colspan=2 height=20px>�������� (<a class=td_tiny_text href=%s?todo=%s&id=%d&fdescr=on%s >�ӣ ��������</a>)</td></tr>\n", href_main_page, href_view_domain_individ, panswer->id_domain, (addit_da?addit_da:""));
	else
		printf("<tr><td class=td_head colspan=2 height=20px>�������� (<a class=td_tiny_text href=%s?todo=%s&id=%d%s >������</a>)</td></tr>\n", href_main_page, href_view_domain_individ, panswer->id_domain, (addit_da?addit_da:""));
	printf("<tr><td class=td_data_l width=40px></td><td class=td_data_l></td></tr>\n");
	
	printf("<tr><td class=td_data_l colspan=2>�������������� ���������� � ������</td></tr>");
	printf("<tr><td class=td_data_l></td><td class=td_data_l >%s</td></tr>\n", panswer->add_info);
	
	if (panswer->domain_status == panswer->enum_released || view_full_descr == TRUE) {
		printf("<tr><td class=td_data_l colspan=2>������� ������������ ������</td></tr>");
		printf("<tr><td class=td_data_l></td><td class=td_data_l >%s</td></tr>\n", panswer->why_released);
	}
	
	if (panswer->domain_status == panswer->enum_refused || view_full_descr == TRUE) {
		printf("<tr><td class=td_data_l colspan=2>������� ���������� ������</td></tr>");
		printf("<tr><td class=td_data_l></td><td class=td_data_l_red >%s</td></tr>\n", panswer->why_refused);
	}
	
	printf("<tr><td class=td_data_l colspan=2>������� (<a class=td_tiny_text_dark href=%s?todo=%s&id=%d>��������</a>)</td></tr>", href_main_page, href_add_notes, panswer->id_domain);
	printf("<tr><td class=td_data_l></td><td class=td_data_l >%s</td></tr>\n", panswer->notes);
	printf("</table>\n");
	
	printf("</td></tr>\n");
	printf("</table>\n");
	// Second Column END

};

void html_print_search_domain()
{
	printf("<center class=first>����� �������</center>");
	printf("<br>\n\n");

	printf(
	"<form action=\"%s\" method=GET name=\"search_domain\" id=\"search_domain\">\n"
	"<input type=hidden name=\"todo\" value=\"%s\">"
	"<table class=standart cellspacing=0 cellpadding=0 width=520px>\n"
	"<tr> <td class=td_head colspan=2>������� ������ ��� ������</td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l><input type=reset value=\"��������\" ></td>\n"
	"<td class=td_data_l><input type=submit value=\"������ �����\"></td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"


	"<tr height=5px> <td class=td_data_l colspan=2><b>���������� � ������</b></td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l width=200px>��������� ������</td>\n"
	"<td class=td_data_l><select name=\"domain_status\">\n"
	"<option value=\"all\" selected>�����</option>\n"
	"<option value=\"ok\">��������</option>\n"
	"<option value=\"wait_for_pay\">������� ������</option>\n"
	"<option value=\"queued\">�� �����������</option>\n"
	"<option value=\"checked\">�������� ��������</option>\n"
	"<option value=\"suspended\">��������� ��������</option>\n"
	"<option value=\"refused\">��������</option>\n"
	"<option value=\"recalled\">������ ��������</option>\n"
	"<option value=\"hold\">�������������</option>\n"
	"<option value=\"frozen\">���������</option>\n"
	"<option value=\"released\">��������</option>\n"
	"</td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>������ ���</td>\n"
	"<td class=td_data_l><input type=text name=\"id_domain\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>������ �������� ���</td>\n"
	"<td class=td_data_l><input type=text name=\"full_name\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>�������������� ���</td>\n"
	"<td class=td_data_l><input type=text name=\"d_name\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>�������� ����</td>\n"
	"<td class=td_data_l><input type=text name=\"d_zone\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	
	"<tr height=5px> <td class=td_data_l colspan=2><b>���������� � �������</b></td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>����� �������</td>\n"
	"<td class=td_data_l><input type=text name=\"id_person\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>���</td>\n"
	"<td class=td_data_l><input type=text name=\"firstname\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�������</td>\n"
	"<td class=td_data_l><input type=text name=\"lastname\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>��������</td>\n"
	"<td class=td_data_l><input type=text name=\"company\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>������</td>\n"
	"<td class=td_data_l><input type=text name=\"country\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�������</td>\n"
	"<td class=td_data_l><input type=text name=\"region\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�������� ������</td>\n"
	"<td class=td_data_l><input type=text name=\"postal\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�����</td>\n"
	"<td class=td_data_l><input type=text name=\"city\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�����</td>\n"
	"<td class=td_data_l><input type=text name=\"address1\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�����2</td>\n"
	"<td class=td_data_l><input type=text name=\"address2\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>���������� �������</td>\n"
	"<td class=td_data_l><input type=text name=\"phone\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>����� ���������</td>\n"
	"<td class=td_data_l><input type=text name=\"fax\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>E-mail</td>\n"
	"<td class=td_data_l><input type=text name=\"email\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>NIC-handle</td>\n"
	"<td class=td_data_l><input type=text name=\"nic\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l><input type=reset value=\"��������\" ></td>\n"
	"<td class=td_data_l><input type=submit value=\"������ �����\" ></td></tr>\n"
	"</table>\n"
	"</form>\n",
	
	href_main_page, href_search_domain_run);
};

void html_print_search_domain_run()
{
	char * domain_status = NULL;
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;

	domain_status = get_value("DOMAIN_STATUS");

	printf("\n<center class=first>��������� ������</center>\n");

	panswer = parse_result("search_domain");

	printf("<span class=text>�������� ������������: %d</span><br>", sql_get_rows_count());
//	printf("<span class=text>������: %s</span><br>", panswer->source_query);
		
	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=second>����� �� ��� �����������</center>");
		return;
	}
	
	if (strcmp(domain_status, "wait_for_pay") == 0) {
		printf("\n<script language=javascript>\n");
		printf("	function create_domain(domain_count) {\n");
		printf("		var id = \"&id=\";\n");
		printf("		var empty = \"true\";\n");
		printf("		form = document.getElementById(\"create_domain_form\");\n");
		printf("		for (var i=0; i < domain_count; i++) {\n");
		printf("			check = document.getElementById(\"checkbox_\" + i);\n");
		printf("			if (check.checked == true) {\n");
		printf("				id = id + check.name + \":\";\n");
		printf("				empty = \"false\";\n");
		printf("			}\n");
		printf("		}\n");
		printf("		\n");
		printf("		if (empty == \"true\") {\n");
		printf("			alert(\"�� ������ ������� �����(�)\");\n");
		printf("			return;\n");
		printf("		}\n");
		printf("		url = \"%s?todo=%s\";\n", href_main_page, href_accept_query);
		printf("		url = url + id;\n");
		printf("		form.action = url;\n");
		printf("		form.submit();\n");
		printf("	}\n");
		printf("</script>\n\n");
	}

	printf("<br>\n");
	
	if (strcmp(domain_status, "wait_for_pay") == 0)
		printf("<form method=POST name=\"create_domain_form\" id=\"create_domain_form\">\n");
	
	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 

	printf("<tr>\n");
	printf("<td class=td_head >������-���</td>\n");
	printf("<td class=td_head >�������� ���</td>\n");
	printf("<td class=td_head >����</td>\n");

	if (strcmp(domain_status, "wait_for_pay") == 0)
		printf("<td class=td_head >����� �������</td>\n");

	if (strcmp(domain_status, "refused") == 0)
		printf("<td class=td_head >���� ������</td>\n");

	if (strcmp(domain_status, "queued") == 0) {
		printf("<td class=td_head >���� ������</td>\n");
		printf("<td class=td_head >���� �������������</td>\n");
	}

	if (strcmp(domain_status, "all") == 0)
		printf("<td class=td_head >���������</td>\n");

	printf("</tr>\n");

	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");

		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->id_domain);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_domain_individ, panswer->id_domain, panswer->d_name);
		printf("<td class=td_data >%s</td>\n", panswer->z_name);

		if (strcmp(domain_status, "wait_for_pay") == 0)
			printf("<td class=td_data ><input type=\"checkbox\" name=\"%d\" id=\"checkbox_%d\"></td>\n", panswer->id_domain, i);

		if (strcmp(domain_status, "refused") == 0)
			printf("<td class=td_data ><input type=\"checkbox\" name=\"%d\" id=\"checkbox_%d\"></td>\n", panswer->refused, i);

		if (strcmp(domain_status, "queued") == 0) {
			printf("<td class=td_data >%s</td>\n", panswer->payed);
			printf("<td class=td_data >%d ���(�)</td>\n", panswer->lease_time);
		}
	
		if (strcmp(domain_status, "all") == 0)
			printf("<td class=td_data >%s</td>\n", panswer->status);
			
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	if (strcmp(domain_status, "wait_for_pay") == 0)
		printf("<tr height=44px> <td></td> <td></td> <td></td>\n"\
			"<td><input type=button value=\"������� �� ����������\" onClick=\"create_domain(%d)\"></td></tr>\n"\
			"</table>\n", i);
	
	if (strcmp(domain_status, "wait_for_pay") == 0)
		printf("</from>\n");
};

void html_print_search_client()
{
	printf("<center class=first>����� ��������</center>");
	printf("<br>\n\n");

	printf(
	"<form action=\"%s\" method=GET name=\"searchclient\" id=\"searchclient\">\n"
	"<input type=hidden name=\"todo\" value=\"%s\">"
	"<table class=standart cellspacing=0 cellpadding=0 width=520px>\n"
	"<tr> <td class=td_head colspan=2>������� ������ ��� ������</td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l><input type=reset value=\"��������\" ></td>\n"
	"<td class=td_data_l><input type=submit value=\"������ �����\"></td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"


	"<tr height=5px> <td class=td_data_l colspan=2><b>���������� � ������</b></td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>������ �������� ���</td>\n"
	"<td class=td_data_l><input type=text name=\"full_name\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	
	"<tr height=5px> <td class=td_data_l colspan=2><b>���������� � �������</b></td></tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>����� �������</td>\n"
	"<td class=td_data_l><input type=text name=\"id_person\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>���</td>\n"
	"<td class=td_data_l><input type=text name=\"firstname\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�������</td>\n"
	"<td class=td_data_l><input type=text name=\"lastname\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>��������</td>\n"
	"<td class=td_data_l><input type=text name=\"company\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>������</td>\n"
	"<td class=td_data_l><input type=text name=\"country\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�������</td>\n"
	"<td class=td_data_l><input type=text name=\"region\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�������� ������</td>\n"
	"<td class=td_data_l><input type=text name=\"postal\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�����</td>\n"
	"<td class=td_data_l><input type=text name=\"city\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�����</td>\n"
	"<td class=td_data_l><input type=text name=\"address1\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>�����2</td>\n"
	"<td class=td_data_l><input type=text name=\"address2\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>���������� �������</td>\n"
	"<td class=td_data_l><input type=text name=\"phone\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"

	"<tr> <td class=td_data_l>����� ���������</td>\n"
	"<td class=td_data_l><input type=text name=\"fax\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>E-mail</td>\n"
	"<td class=td_data_l><input type=text name=\"email\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l>NIC-handle</td>\n"
	"<td class=td_data_l><input type=text name=\"nic\" maxlength=255 size=40></td><tr>\n"
	"<tr height=5px> <td class=td_data_l colspan=2></td></tr>\n"
	
	"<tr> <td class=td_data_l><input type=reset value=\"��������\" ></td>\n"
	"<td class=td_data_l><input type=submit value=\"������ �����\" ></td></tr>\n"
	"</table>\n"
	"</form>\n",
	
	href_main_page, href_search_client_run);
};

void html_print_search_client_run()
{
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;

	printf("\n<center class=first>��������� ������</center>\n");

	panswer = parse_result("search_client");

	printf("<span class=text>�������� ������������: %d</span><br>", sql_get_rows_count());
//	printf("<span class=text>������: %s</span><br>", panswer->source_query);
		
	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=second>����� �� ��� �����������</center>");
		return;
	}

	printf("<br>\n");

	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 
	
	printf("<tr>\n");
	printf("<td class=td_head >�����</td>\n");
	printf("<td class=td_head >���</td>\n");
	printf("<td class=td_head >�������</td>\n");
	printf("<td class=td_head >E-mail</td>\n");
	printf("<td class=td_head >NIC-handle</td>\n");
	printf("<td class=td_head >�������</td>\n");
	printf("<td class=td_head >�������</td>\n");
	printf("</tr>\n");

	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
	
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->id_person);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->firstname);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->lastname);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->email);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->userhdl);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s ���.</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->cash);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%d</a></td>\n", 
			href_main_page, href_view_client_individ, panswer->id_person, panswer->domain_registered);
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("</table>\n");
};

void html_print_config_zone()
{
	unsigned int i = 0;
	struct __p_answer * panswer = NULL;

	printf("\n<center class=first>�������� ������� ����</center>\n");

	panswer = parse_result("cfg_zone_list");

	if (panswer->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", panswer->errmsg, panswer->source_query);
		return;
	}
	if (panswer->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>��� ������� ��� ��� ����������</center>");
		return;
	}
	
	printf("<br>\n");
	printf("<table class=standart cellspacing=0px cellpadding=0px>\n"); 
	
	printf("<tr>\n");
	printf("<td class=td_head >�������� ����</td>\n");
	printf("<td class=td_head >���������</td>\n");
	printf("</tr>\n");

	while (panswer) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");

		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_config_zone_view, panswer->id_table, panswer->z_name);
		printf("<td class=td_data ><a class=td_data href=%s?todo=%s&id=%d >%s</a></td>\n", 
			href_main_page, href_config_zone_view, panswer->id_table, (panswer->active?"�������":"����������"));
		printf("</tr>\n");
		panswer = panswer->next;
		i++;
	}

	printf("</table>\n");
};

void html_print_config_zone_view()
{
	char * id = NULL;
	unsigned int current_ns = 0;
	unsigned int current_mx = 0;
	unsigned int i = 0;
	unsigned int count_ns = 0;
	unsigned int count_mx = 0;
	struct __p_answer * pmain = NULL;
	struct __p_answer * pns = NULL;
	struct __p_answer * pns_full = NULL;
	struct __p_answer * pmx = NULL;
	struct __p_answer * pmx_full = NULL;
	struct __p_answer * p_copy = NULL;
	struct __p_answer * p_copy2 = NULL;
	struct __p_answer * panswer = NULL;

	id = get_value("ID");

	if (!id) {
		printf("<center class=error>�� ������ ����� (���������� �����) ����<center>\n");
		return;
	}

	printf("\n<center class=first>��������� ������� ����</center>\n");

	pmain = parse_result("get_main_zone_cfg");

	if (pmain->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", pmain->errmsg, pmain->source_query);
		return;
	}
	if (pmain->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>��� ������. ������ ����� ������ - �� ������ �����</center>");
		return;
	}

	pns = parse_result("get_ns_zone_cfg");

	if (pns->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", pns->errmsg, pns->source_query);
		return;
	}
	if (pns->result_code == SQL_RESULT_EMPTY) {
		free(pns);
		pns = NULL;
	}

	pns_full = parse_result("get_all_ns");

	if (pns_full->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", pns_full->errmsg, pns_full->source_query);
		return;
	}
	if (pns_full->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>�� ������� ����� ��� NS �������</center>");
	}

	pmx = parse_result("get_mx_zone_cfg");

	if (pmx->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", pmx->errmsg, pmx->source_query);
		return;
	}
	if (pmx->result_code == SQL_RESULT_EMPTY) {
		free(pmx);
		pmx = NULL;
	}
	
	pmx_full = parse_result("get_all_mx");

	if (pmx_full->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", pmx_full->errmsg, pmx_full->source_query);
		return;
	}
	if (pmx_full->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>�� ������� ����� ��� MX �������</center>");
	}

	if (pns)
		count_ns = pns->rows_count > 4 ? pns->rows_count+1 : 5;
	else
		count_ns = 5;
	if (pmx)
		count_mx = pmx->rows_count > 4 ? pmx->rows_count+1 : 5;
	else
		count_mx = 5;
	
	if (pns_full)
		if (pns_full->rows_count < count_ns)
			count_ns = pns_full->rows_count;
	if (pmx_full)
		if (pmx_full->rows_count < count_mx)
			count_mx = pmx_full->rows_count;
				
	printf("\n<script language=javascript>\n");
	printf("	function change_title() {\n");
	printf("		ch = document.getElementById(\"active\");\n");
	printf("		tl = document.getElementById(\"active_title\");\n");
	printf("		if (ch.checked == true)\n");
	printf("			tl.innerHTML = \"<font color=#008800>�������� (������ �����������)</font>\";\n");
	printf("		else\n");
	printf("			tl.innerHTML = \"<font color=#AA0000>���������� (������ �� �����������)</font>\";\n");
	printf("		\n");
	printf("	}\n");
	printf("	\n");
	printf("	function Change_NS_record(rec, rec_index) {\n");
	printf("		ns_rec = document.getElementById(rec);\n");
	printf("		ns_count = document.getElementById(\"ns_count\");\n");
	printf("		\n");
	printf("		if (ns_rec.value != \"none\")\n");
	printf("		for (i=0; i < ns_count.value; i++) {\n");
	printf("			if (i == rec_index) {\n");
	printf("				continue;\n");
	printf("			}\n");
	printf("			other_ns_rec = document.getElementById(\"ns_rec_server_\" + i);\n");
	printf("			\n");
	printf("			if (other_ns_rec.value == ns_rec.value) {\n");
	printf("				alert(\"����� NS ������ ��� ������\");\n");
	printf("				old_ns_id = document.getElementById(\"current_ns_rec_server_\" + rec_index);\n");
	printf("				ns_rec.value = old_ns_id.title;\n");
	printf("				return;\n");
	printf("			}\n");
	printf("		}\n");
	printf("		ip_title = document.getElementById(\"ns_rec_ip_\" + rec_index);\n");
	printf("		dname_title = document.getElementById(\"ns_rec_dname_\" + rec_index);\n");
	printf("		current_ns_id = document.getElementById(\"current_ns_rec_server_\" + rec_index);\n");
	printf("		\n");
	printf("		if (ns_rec.value != \"none\") {\n");
	printf("			new_ip = document.getElementById(\"ns_server_\" + ns_rec.value + \"_ip\");\n");
	printf("			new_dname = document.getElementById(\"ns_server_\" + ns_rec.value + \"_domain_name\");\n");
	printf("			\n");
	printf("			ip_title.innerHTML = new_ip.title;\n");
	printf("			dname_title.innerHTML = new_dname.title;\n");
	printf("		}\n");
	printf("		else {\n");
	printf("			ip_title.innerHTML = \"\";\n");
	printf("			dname_title.innerHTML = \"\";\n");
	printf("		}\n");
	printf("		\n");
	printf("		current_ns_id.title = ns_rec.value;\n");
	printf("		\n");
	printf("	}\n");
	printf("	\n");
	printf("	function Change_MX_record(rec, rec_index) {\n");
	printf("		mx_rec = document.getElementById(rec);\n");
	printf("		mx_count = document.getElementById(\"mx_count\");\n");
	printf("		\n");
	printf("		if (mx_rec.value != \"none\")\n");
	printf("		for (i=0; i < mx_count.value; i++) {\n");
	printf("			if (i == rec_index) {\n");
	printf("				continue;\n");
	printf("			}\n");
	printf("			other_mx_rec = document.getElementById(\"mx_rec_server_\" + i);\n");
	printf("			\n");
	printf("			if (other_mx_rec.value == mx_rec.value) {\n");
	printf("				alert(\"����� MX ������ ��� ������\");\n");
	printf("				old_mx_id = document.getElementById(\"current_mx_rec_server_\" + rec_index);\n");
	printf("				mx_rec.value = old_mx_id.title;\n");
	printf("				return;\n");
	printf("			}\n");
	printf("		}\n");
	printf("		ip_title = document.getElementById(\"mx_rec_ip_\" + rec_index);\n");
	printf("		dname_title = document.getElementById(\"mx_rec_dname_\" + rec_index);\n");
	printf("		current_mx_id = document.getElementById(\"current_mx_rec_server_\" + rec_index);\n");
	printf("		\n");
	printf("		if (mx_rec.value != \"none\") {\n");
	printf("			new_ip = document.getElementById(\"mx_server_\" + mx_rec.value + \"_ip\");\n");
	printf("			new_dname = document.getElementById(\"mx_server_\" + mx_rec.value + \"_domain_name\");\n");
	printf("			\n");
	printf("			ip_title.innerHTML = new_ip.title;\n");
	printf("			dname_title.innerHTML = new_dname.title;\n");
	printf("		}\n");
	printf("		else {\n");
	printf("			ip_title.innerHTML = \"\";\n");
	printf("			dname_title.innerHTML = \"\";\n");
	printf("		}\n");
	printf("		current_mx_id.title = mx_rec.value;\n");
	printf("	}\n");
	printf("	\n");
	printf("	function Check_Confirm() {\n");
	printf("		ns_count = document.getElementById(\"ns_count\");\n");
	printf("		mx_count = document.getElementById(\"mx_count\");\n");
	printf("		var primary_count = 0;\n");
	printf("		var ns_server_count = 0;\n");
	printf("		var mx_server_count = 0;\n");
	printf("		for (i=0; i < ns_count.value; i++) {\n");
	printf("			ns_role = document.getElementById(\"ns_rec_role_\" + i);\n");
	printf("			ns_server = document.getElementById(\"ns_rec_server_\" + i);\n");
	printf("			\n");
	printf("			if (ns_server.value != \"none\") {\n");
	printf("				if (ns_role.value == \"none\") {\n");
	printf("					alert(\"�� �� ������� ���� NS �������\");\n");
	printf("					return;\n");
	printf("				}\n");
	printf("				if (ns_role.value == 1)\n");
	printf("					primary_count++;\n");
	printf("				ns_server_count++;\n");
	printf("			}\n");
	printf("		}\n");
	printf("		if (ns_server_count == 0) {\n");
	printf("			alert(\"�� �� ������� NS �������. ������� ��� NS �������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		if (ns_server_count == 1) {\n");
	printf("			alert(\"�� ������� ���� NS ������. ������� ��� NS �������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		if (primary_count == 0) {\n");
	printf("			alert(\"�� �� ������� ��������� NS ������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		if (primary_count > 1) {\n");
	printf("			alert(\"�� ������� ����� ������ ���������� NS �������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		\n");
	printf("		for (i=0; i < mx_count.value; i++) {\n");
	printf("			mx_prior = document.getElementById(\"mx_rec_prior_\" + i);\n");
	printf("			mx_server = document.getElementById(\"mx_rec_server_\" + i);\n");
	printf("			\n");
	printf("			if (mx_server.value != \"none\") {\n");
	printf("				if (mx_prior.value == \"\") {\n");
	printf("					alert(\"�� �� ������� ��������� MX �������\");\n");
	printf("					return;\n");
	printf("				}\n");
	printf("				if (mx_prior.value < 0) {\n");
	printf("					alert(\"�� ������� �� ������ ��������� ��� MX �������\");\n");
	printf("					return;\n");
	printf("				}\n");
	printf("				if (mx_prior.value > 65535) {\n");
	printf("					alert(\"�� ������� �� ������ ��������� ��� MX �������\");\n");
	printf("					return;\n");
	printf("				}\n");
	printf("				mx_server_count++;\n");
	printf("			}\n");
	printf("		}\n");
	printf("		if (mx_server_count == 0) {\n");
	printf("			alert(\"�� �� ������� MX ������. ������� ���� MX ������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		\n");
	printf("		for (i=0; i < mx_count.value - 1; i++) {\n");
	printf("			mx_prior_i = document.getElementById(\"mx_rec_prior_\" + i);\n");
	printf("			mx_server_i = document.getElementById(\"mx_rec_server_\" + i);\n");
	printf("			if (mx_server_i.value == \"none\")\n");
	printf("				continue;\n");
	printf("			for (k=i+1; k < mx_count.value; k++) {\n");
	printf("				mx_prior_k = document.getElementById(\"mx_rec_prior_\" + k);\n");
	printf("				mx_server_k = document.getElementById(\"mx_rec_server_\" + k);\n");
	printf("				if (mx_server_k.value == \"none\")\n");
	printf("					continue;\n");
	printf("				if (mx_prior_i.value == mx_prior_k.value) {\n");
	printf("					alert(\"�� ������� ��� ���������� ���������� ��� MX ��������\");\n");
	printf("					return;\n");
	printf("				}\n");
	printf("			}\n");
	printf("		}\n");
	printf("		\n");
	printf("		document.zone_setup.submit();\n");
	printf("	}\n");
	printf("</script>\n\n");
	printf("<br>\n");

	printf("<form method=GET action=\"engine\" name=\"zone_setup\">\n"); 
	printf("<input type=hidden value=\"%s\" name=\"todo\">\n", href_config_zone_update);
	printf("<input type=hidden value=\"%s\" name=\"id\">\n", id);

	i = 0;
	printf("\n<span id=\"ns_storage\">\n");
	printf("	<span id=\"ns_record_count\" title=\"%d\"></span>\n", (pns_full?pns_full->rows_count:0));
	panswer = pns_full;
	while (panswer) {
		printf("	<span id=\"ns_server_%d\">\n", i);
		printf("		<span id=\"ns_server_%d_title\" title=\"%s\"></span>\n", panswer->id_ns, panswer->ns_title);
		printf("		<span id=\"ns_server_%d_domain_name\" title=\"%s\"></span>\n", panswer->id_ns, panswer->ns_domain_name);
		printf("		<span id=\"ns_server_%d_ip\" title=\"%s\"></span>\n", panswer->id_ns, panswer->ns_ip);
		printf("	</span>\n");
		panswer = panswer->next;
		i++;
	}
	printf("</span>\n");

	i = 0;
	printf("\n<span id=\"mx_storage\">\n");
	printf("	<span id=\"mx_record_count\" title=\"%d\"></span>\n", (pmx_full?pmx_full->rows_count:0));
	panswer = pmx_full;
	while (panswer) {
		printf("	<span id=\"mx_server_%d\">\n", i);
		printf("		<span id=\"mx_server_%d_title\" title=\"%s\"></span>\n", panswer->id_mx, panswer->mx_title);
		printf("		<span id=\"mx_server_%d_domain_name\" title=\"%s\"></span>\n", panswer->id_mx, panswer->mx_domain_name);
		printf("		<span id=\"mx_server_%d_ip\" title=\"%s\"></span>\n", panswer->id_mx, panswer->mx_ip);
		printf("	</span>\n");
		panswer = panswer->next;
		i++;
	}
	printf("</span>\n");

	panswer = pns;
	printf("\n<span id=\"current_ns\">\n");
	printf("<input type=hidden name=\"ns_count\" id=\"ns_count\" value=\"%d\">\n", count_ns);
	for (i=0; i < count_ns; i++) {
		if (panswer) {
			printf("	<span id=\"current_ns_rec_server_%d\" title=\"%d\"></span>\n", i, panswer->id_ns);
			panswer = panswer->next;
		}
		else
			printf("	<span id=\"current_ns_rec_server_%d\" title=\"none\"></span>\n", i);
	}
	printf("</span>\n");

	panswer = pmx;
	printf("\n<span id=\"current_mx\">\n");
	printf("<input type=hidden name=\"mx_count\" id=\"mx_count\" value=\"%d\">\n", count_mx);
	for (i=0; i < count_mx; i++) {
		if (panswer) {
			printf("	<span id=\"current_mx_rec_server_%d\" title=\"%d\"></span>\n", i, panswer->id_mx);
			panswer = panswer->next;
		}
		else
			printf("	<span id=\"current_mx_rec_server_%d\" title=\"none\"></span>\n", i);
	}
	printf("</span>\n");

	printf("<table class=standart>\n"); 
	printf("<tr><td class=td_head colspan=2 height=20px>�������� ����</td>");
	printf("<td class=td_data_c valign=top rowspan=11 ><input type=button align=left value=\"  ������ ���������  \" OnClick=\"Check_Confirm()\"></td></tr>\n");
	
	printf("<tr><td class=td_data_l >���������� �����</td>");
	printf("<td class=td_data_l >%d</td></tr>\n", (pmain?pmain->id_table:0));
	printf("<tr><td class=td_data_l >����</td>");
	printf("<td class=td_data_l >%s</td></tr>\n", (pmain?pmain->z_name:"��� ����"));
	printf("<tr><td class=td_data_l >���� �������������</td>");
	printf("<td class=td_data_l ><input type=text value=\"%d\" name=\"max_lease_time\" maxlength=5> ���(�)</td></tr>\n", (pmain?pmain->max_lease_time:0));
	printf("<tr><td class=td_data_l >Initial</td>");
	printf("<td class=td_data_l ><input type=text value=\"%d\" name=\"initial\" maxlength=5> ����</td></tr>\n", (pmain?pmain->initial:0));
	printf("<tr><td class=td_data_l >Grace</td>");
	printf("<td class=td_data_l ><input type=text value=\"%d\" name=\"grace\" maxlength=5> ����</td></tr>\n", (pmain?pmain->grace:0));
	printf("<tr><td class=td_data_l >Pending</td>");
	printf("<td class=td_data_l ><input type=text value=\"%d\" name=\"pending\" maxlength=5> ����</td></tr>\n", (pmain?pmain->pending:0));
	printf("<tr><td class=td_data_l >��������� � ���</td>");
	printf("<td class=td_data_l ><input type=text value=\"%s\" name=\"price\" maxlength=5> ���.</td></tr>\n", (pmain?pmain->price:0));
	printf("<tr><td class=td_data_l >Email ��� ������ ������</td>");
	printf("<td class=td_data_l ><input type=text value=\"%s\" name=\"admin_email\" maxlength=255></td></tr>\n", (pmain?pmain->admin_email:"��� ������"));
	printf("<tr><td class=td_data_l >���������</td>");
	if (pmain)
		printf("<td class=td_data_l colspan=2><input type=checkbox %s name=\"active\" id=\"active\" onChange=\"change_title()\"> <span id=\"active_title\">%s</span></td></tr>\n", (pmain->active?"checked":""), (pmain->active?"<font color=#008800>�������� (������ �����������)</font>":"<font color=#AA0000>���������� (������ �� �����������)</font>"));
	else
		printf("<td class=td_data_l colspan=2><input type=checkbox name=\"active\" id=\"active\" onChange=\"change_title()\"> <span id=\"active_title\"><font color=#AA0000>���������� (������ �� �����������)</font></span></td></tr>\n");
	printf("</table>\n");

	printf("<p>\n");
	
	printf("<table class=standart>\n"); 
	printf("<tr><td class=td_head colspan=5 height=20px>NS-������� ������������, ��� ������������� � ���� %s</td></tr>\n", (pmain?pmain->z_name:"��� ����"));
	printf("<td class=td_head>&#035;</td>\n");
	printf("<td class=td_head>����</td>\n");
	printf("<td class=td_head>������</td>\n");
	printf("<td class=td_head>IP �����</td>\n");
	printf("<td class=td_head>�������� ���</td>\n");
	
	panswer = pns;
	p_copy = pns_full;
	for (i=0; i < count_ns; i++) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
		
		printf("<td class=td_data_c >%d</td>\n", i+1);
		
		printf("<td class=td_data_l ><select id=\"ns_rec_role_%d\" name=\"ns_rec_%d_role\" >\n", i, i, i);
		
		if (panswer) {
			if (panswer->ns_master) {
				printf("<option value=\"none\">--------------</option>\n");
				printf("<option value=\"1\" selected>���������</option>\n");
				printf("<option value=\"0\">���������</option>\n");
			}
			else {
				printf("<option value=\"none\">--------------</option>\n");
				printf("<option value=\"1\">���������</option>\n");
				printf("<option value=\"0\" selected>���������</option>\n");
			}
			
			current_ns = panswer->id_ns;
		}
		else {
			printf("<option value=\"none\" selected>--------------</option>\n");
			printf("<option value=\"1\">���������</option>\n");
			printf("<option value=\"0\">���������</option>\n");
			
			current_ns = 0;
		}
		
		p_copy2 = p_copy;
		
		printf("<td class=td_data_l ><select id=\"ns_rec_server_%d\" name=\"ns_rec_%d_server\" onChange=\"Change_NS_record('ns_rec_server_%d', %d)\">\n", i, i, i, i);
		printf("<option value=\"none\">--------------</option>\n");
		
		while (p_copy2) {
			if (current_ns == p_copy2->id_ns)
				printf("<option value=\"%d\" selected>%s</option>\n", p_copy2->id_ns, p_copy2->ns_title);
			else
				printf("<option value=\"%d\">%s</option>\n", p_copy2->id_ns, p_copy2->ns_title);
			p_copy2 = p_copy2->next;
		}
		printf("</select></td>\n");
		
		if (panswer) {
			printf("<td class=td_data_l id=\"ns_rec_ip_%d\" >%s</td>\n", i, panswer->ns_ip);
			printf("<td class=td_data_l id=\"ns_rec_dname_%d\" >%s</td>\n", i, panswer->ns_domain_name);
		}
		else {
			printf("<td class=td_data_l id=\"ns_rec_ip_%d\" ></td>\n", i);
			printf("<td class=td_data_l id=\"ns_rec_dname_%d\" ></td>\n", i);
		}
		printf("</tr>\n");
		if (panswer)
			panswer = panswer->next;
	}	printf("</table>\n");

	printf("<p>\n");
	
	printf("<table class=standart>\n"); 
	printf("<tr><td class=td_head colspan=5 height=20px>MX-������� ������������, ��� ������������� � ���� %s</td></tr>\n", (pmain?pmain->z_name:"��� ����"));
	printf("<td class=td_head>&#035;</td>\n");	
	printf("<td class=td_head>���������</td>\n");
	printf("<td class=td_head>������</td>\n");
	printf("<td class=td_head>IP �����</td>\n");
	printf("<td class=td_head>�������� ���</td>\n");

	panswer = pmx;
	p_copy = pmx_full;
	for (i=0; i < count_mx; i++) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");
		
		printf("<td class=td_data_c >%d</td>\n", i+1);
		
		if (panswer)
			printf("<td class=td_data_c ><input type=text id=\"mx_rec_prior_%d\" name=\"mx_rec_%d_prior\" value=\"%d\" size=5 maxlength=5></td>\n", i, i, panswer->mx_prior);
		else
			printf("<td class=td_data_c ><input type=text id=\"mx_rec_prior_%d\" name=\"mx_rec_%d_prior\" value=\"\" size=5 maxlength=5></td>\n", i, i );
		
		if (panswer)
			current_mx = panswer->id_mx;
		else
			current_mx = 0;
		
		p_copy2 = p_copy;
		
		printf("<td class=td_data_l ><select id=\"mx_rec_server_%d\" name=\"mx_rec_%d_server\" onChange=\"Change_MX_record('mx_rec_server_%d', %d)\">\n", i, i, i, i);
		printf("<option value=\"none\">--------------</option>\n");
		
		while (p_copy2) {
			if (current_mx == p_copy2->id_mx)
				printf("<option value=\"%d\" selected>%s</option>\n", p_copy2->id_mx, p_copy2->mx_title);
			else
				printf("<option value=\"%d\">%s</option>\n", p_copy2->id_mx, p_copy2->mx_title);
			p_copy2 = p_copy2->next;
		}
		printf("</select></td>\n");
		
		if (panswer) {
			printf("<td class=td_data_l id=\"mx_rec_ip_%d\" >%s</td>\n", i, panswer->mx_ip);
			printf("<td class=td_data_l id=\"mx_rec_dname_%d\" >%s</td>\n", i, panswer->mx_domain_name);
		}
		else {
			printf("<td class=td_data_l id=\"mx_rec_ip_%d\" ></td>\n", i);
			printf("<td class=td_data_l id=\"mx_rec_dname_%d\" ></td>\n", i);
		}
		printf("</tr>\n");
		if (panswer)
			panswer = panswer->next;
	}
	printf("</table>\n");
	
	printf("</form>\n");
	
	safe_free(&id);
};

void html_print_config_zone_update()
{
	struct __p_answer * psql = NULL;
	char * id = NULL;
	char * max_lease_time = NULL;
	char * initial = NULL;
	char * grace = NULL;
	char * pending = NULL;
	char * price = NULL;
	char * admin_email = NULL;
	char * active = NULL;
	char * ns_count = NULL;
	char * mx_count = NULL;
	char * temp = NULL;
	char * temp2 = NULL;
	unsigned int warnings = 0;
	unsigned int i = 0;

	id = get_value("ID");
	max_lease_time = get_value("MAX_LEASE_TIME");
	initial = get_value("INITIAL");
	grace = get_value("GRACE");
	pending = get_value("PENDING");
	price = get_value("PRICE");
	admin_email = get_value("ADMIN_EMAIL");
	active = get_value("ACTIVE");
	ns_count = get_value("NS_COUNT");
	mx_count = get_value("MX_COUNT");

	if (	!id || 
		!max_lease_time || 
		!initial || 
		!grace ||
		!pending ||
		!price ||
		!admin_email ||
		!ns_count ||
		!mx_count) {
		
		printf("<span class=error>�� ������ ���������. ��������, ��� ������ ���������� �����.</span><br>\n");
		return;
	}

	if (strlen(max_lease_time) == 0) {
		printf("<span class=error>�� ����� ���� �������������. ������� ��������.</span><br>\n");
		return;
	}

	if (strlen(admin_email) == 0) {
		printf("<span class=error>�� ����� Email ��� ������ �����. ������� ��������.</span><br>\n");
		return;
	}

	if (strlen(initial) == 0) {
		printf("<span class=error_simple>�� ����� Initial - �������������� � ����</span><br>\n");
		warnings++;
	}
	if (strlen(grace) == 0) {
		printf("<span class=error_simple>�� ����� Grace - �������������� � ����</span><br>\n");
		warnings++;
	}
	if (strlen(pending) == 0) {
		printf("<span class=error_simple>�� ����� Pending - �������������� � ����</span><br>\n");
		warnings++;
	}
	if (strlen(price) == 0) {
		printf("<span class=error_simple>�� ������ ��������� - ������������� � ���� <font color=#AA0000>���������</font></span><br>\n");
		warnings++;
	}

	for (i=0; i < atoi(ns_count); i++) {
		temp = strdup("NS_REC_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		temp = sts(&temp, "_ROLE");
		temp2 = get_value(temp);
		safe_free(&temp);
		if (!temp2) {
			printf("<span class=error>�� ������ ��������� NS ��������. ��������, ��� ������ ���������� �����.</span><br>\n");
			return;
		}
		if (strlen(temp2) == 0) {
			printf("<br><center>�� ������ ��������� NS �������� - ������ ����� ������ ���������� �����</center>\n");
			return;
		}
		safe_free(&temp2);
///////////////////////////////////////////////////////////
		temp = strdup("NS_REC_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		temp = sts(&temp, "_SERVER");
		temp2 = get_value(temp);
		safe_free(&temp);
		if (!temp2) {
			printf("<span class=error>�� ������ ��������� NS ��������. ��������, ��� ������ ���������� �����.</span><br>\n");
			return;
		}
		if (strlen(temp2) == 0) {
			printf("<br><center>�� ������ ��������� NS �������� - ������ ����� ������ ���������� �����</center>\n");
			return;
		}
		safe_free(&temp2);
	}

	for (i=0; i < atoi(mx_count); i++) {
		temp = strdup("MX_REC_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		temp = sts(&temp, "_PRIOR");
		temp2 = get_value(temp);
		safe_free(&temp);
		if (!temp2) {
			printf("<span class=error>�� ������ ��������� MX ��������. ��������, ��� ������ ���������� �����.</span><br>\n");
			return;
		}
		safe_free(&temp2);
///////////////////////////////////////////////////////////
		temp = strdup("MX_REC_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		temp = sts(&temp, "_SERVER");
		temp2 = get_value(temp);
		safe_free(&temp);
		if (!temp2) {
			printf("<span class=error>�� ������ ��������� MX ��������. ��������, ��� ������ ���������� �����.</span><br>\n");
			return;
		}
		if (strlen(temp2) == 0) {
			printf("<br><center>�� ������ ��������� MX �������� - ������ ����� ������ ���������� �����</center>\n");
			return;
		}
		safe_free(&temp2);
	}
	
//*************************************************//
//*************************************************//
	
	psql = sql_execute("cfg_zone_update_main");
	if (psql->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", psql->errmsg, psql->source_query);
		return;
	}

	psql = sql_execute("cfg_zone_clear_ns");
	if (psql->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", psql->errmsg, psql->source_query);
		return;
	}

	psql = sql_execute("cfg_zone_update_ns");
	if (psql->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", psql->errmsg, psql->source_query);
		return;
	}

	psql = sql_execute("cfg_zone_clear_mx");
	if (psql->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", psql->errmsg, psql->source_query);
		return;
	}

	psql = sql_execute("cfg_zone_update_mx");
	if (psql->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", psql->errmsg, psql->source_query);
		return;
	}

//*************************************************//
//*************************************************//

	if (warnings) {
		printf("<form>\n");
		printf("<script language=javascript>\n");
		printf("	function cont() {\n");
		printf("		document.location=\"%s?todo=%s&id=%s\";\n", href_main_page, href_config_zone_view, id);
		printf("	}\n");
		printf("</script>\n");
		printf("<br><center><input type=button value=\"  ����������  \" onClick=\"cont()\"></center>\n");
		printf("</form>\n");
	}
	else
		html_print_config_zone_view();
};

void html_print_config_ns_mx()
{
	unsigned int i = 0;
	unsigned int count_ns = 0;
	unsigned int count_mx = 0;
	struct __p_answer * panswer = NULL;
	struct __p_answer * pmx = NULL;
	struct __p_answer * pns = NULL;

	printf("\n<center class=first>��������� NS � MX �������� ������������</center>\n");

	pns = parse_result("get_all_ns");

	if (pns->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", pns->errmsg, pns->source_query);
		return;
	}
	if (pns->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>�� ������� ����� ��� MX �������</center>");
	}
	
	pmx = parse_result("get_all_mx");

	if (pmx->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", pmx->errmsg, pmx->source_query);
		return;
	}
	if (pmx->result_code == SQL_RESULT_EMPTY) {
		printf("<center class=error>�� ������� ����� ��� MX �������</center>");
	}

	if (pns)
		count_ns = pns->rows_count > 4 ? pns->rows_count+1 : 5;
	else
		count_ns = 5;
	if (pmx)
		count_mx = pmx->rows_count > 4 ? pmx->rows_count+1 : 5;
	else
		count_mx = 5;
	
	printf("\n<script language=javascript>\n");
	printf("	function check_fields() {\n");
	printf("		ns_count = document.getElementById(\"ns_count\");\n");
	printf("		ns_servers = 0;\n");
	printf("		for (i=0; i < ns_count.value; i++) {\n");
	printf("			title = document.getElementById(\"ns_title_\" + i);\n");
	printf("			domain = document.getElementById(\"ns_domain_\" + i);\n");
	printf("			ip = document.getElementById(\"ns_ip_\" + i);\n");
	printf("			\n");
	printf("			if (title.value == \"\" && domain.value == \"\" && ip.value == \"\")\n");
	printf("				continue;\n");
	printf("			\n");
	printf("			if (title.value == \"\") {\n");
	printf("				alert(\"�� �� ������� �������� NS �������\");\n");
	printf("				return;\n");
	printf("			}\n");
	printf("			if (domain.value == \"\") {\n");
	printf("				alert(\"�� �� ������� �������� ��� NS �������\");\n");
	printf("				return;\n");
	printf("			}\n");
	printf("			if (ip.value == \"\") {\n");
	printf("				alert(\"�� �� ������� IP ����� NS �������\");\n");
	printf("				return;\n");
	printf("			}\n");
	printf("			ns_servers++;\n");
	printf("		}\n");
	printf("		if (ns_servers == 0) {\n");
	printf("			alert(\"�� �� ������� NS �������. ������� ��� NS �������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		if (ns_servers == 1) {\n");
	printf("			alert(\"�� ������� ���� NS ������. ������� ��� NS �������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		\n");
	printf("		mx_count = document.getElementById(\"mx_count\");\n");
	printf("		mx_servers = 0;\n");
	printf("		for (i=0; i < mx_count.value; i++) {\n");
	printf("			title = document.getElementById(\"mx_title_\" + i);\n");
	printf("			domain = document.getElementById(\"mx_domain_\" + i);\n");
	printf("			ip = document.getElementById(\"mx_ip_\" + i);\n");
	printf("			\n");
	printf("			if (title.value == \"\" && domain.value == \"\" && ip.value == \"\")\n");
	printf("				continue;\n");
	printf("			\n");
	printf("			if (title.value == \"\") {\n");
	printf("				alert(\"�� �� ������� �������� MX �������\");\n");
	printf("				return;\n");
	printf("			}\n");
	printf("			if (domain.value == \"\") {\n");
	printf("				alert(\"�� �� ������� �������� ��� MX �������\");\n");
	printf("				return;\n");
	printf("			}\n");
	printf("			if (ip.value == \"\") {\n");
	printf("				alert(\"�� �� ������� IP ����� MX �������\");\n");
	printf("				return;\n");
	printf("			}\n");
	printf("			mx_servers++;\n");
	printf("		}\n");
	printf("		if (mx_servers == 0) {\n");
	printf("			alert(\"�� �� ������� MX �������. ������� ���� MX ������\");\n");
	printf("			return;\n");
	printf("		}\n");
	printf("		\n");
	printf("		document.ns_mx_setup.submit();\n");
	printf("	}\n");
	printf("	function delete_rec(type, id) {\n");
	printf("		title = document.getElementById(type + \"_title_\" + id);\n");
	printf("		domain = document.getElementById(type + \"_domain_\" + id);\n");
	printf("		ip = document.getElementById(type + \"_ip_\" + id);\n");
	printf("		title.value = \"\";\n");
	printf("		domain.value = \"\";\n");
	printf("		ip.value = \"\";\n");
	printf("	}\n");
	printf("</script>\n\n");

	printf("<form method=GET action=\"engine\" name=\"ns_mx_setup\">\n"); 
	printf("<input type=hidden value=\"%s\" name=\"todo\">\n", href_config_ns_mx_update);
	printf("<input type=hidden name=\"ns_count\" id=\"ns_count\" value=\"%d\">\n", count_ns);
	printf("<input type=hidden name=\"mx_count\" id=\"mx_count\" value=\"%d\">\n", count_mx);
	
	printf("<input type=\"button\" onClick=\"check_fields()\" value=\"  ��������  \">\n");
	printf("<p>\n");
	printf("<input type=\"checkbox\">�����, �������� �������������� ������\n");
	
	printf("<table class=standart>\n"); 
	printf("<tr>\n");
	printf("<td colspan=\"5\" class=td_head_l >NS �������. �����: %d</td>\n");
	printf("</tr>\n");
	printf("<tr>\n");
	printf("<td class=td_head >&#035;</td>\n");
	printf("<td class=td_head >��������</td>\n");
	printf("<td class=td_head >�������� ���</td>\n");
	printf("<td class=td_head >IP �����</td>\n");
	printf("<td class=td_head >��������</td>\n");
	printf("</tr>\n");

	panswer = pns;
	for (i=0; i < count_ns; i++) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");

		printf("<td class=td_data >%d</td>\n", i+1);
		printf("<td class=td_data ><input type=\"hidden\" name=\"ns_id_%d\" value=\"%d\">\n", i, (panswer?panswer->id_ns:0));
		printf("<input type=\"text\" maxlength=\"255\" id=\"ns_title_%d\" name=\"ns_title_%d\" value=\"%s\"></td>\n", i, i, (panswer?panswer->ns_title:""));
		printf("<td class=td_data ><input type=\"text\" maxlength=\"255\" id=\"ns_domain_%d\" name=\"ns_domain_%d\" value=\"%s\"></td>\n", i, i, (panswer?panswer->ns_domain_name:""));
		printf("<td class=td_data ><input type=\"text\" maxlength=\"15\" id=\"ns_ip_%d\" name=\"ns_ip_%d\" value=\"%s\"></td>\n", i, i, (panswer?panswer->ns_ip:""));
		printf("<td class=td_data ><input type=\"button\" value=\"��������\" onClick=\"delete_rec('ns' ,%d)\"></td>\n", i);
		printf("</tr>\n");
		if (panswer)
			panswer = panswer->next;
	}

	printf("<br>\n");
	printf("<br>\n");
	printf("<table class=standart>\n"); 
	printf("<tr>\n");
	printf("<td colspan=\"5\" class=td_head_l >MX �������. �����: %d</td>\n", pmx->rows_count);
	printf("</tr>\n");
	printf("<tr>\n");
	printf("<td class=td_head >&#035;</td>\n");
	printf("<td class=td_head >��������</td>\n");
	printf("<td class=td_head >�������� ���</td>\n");
	printf("<td class=td_head >IP �����</td>\n");
	printf("<td class=td_head >��������</td>\n");
	printf("</tr>\n");

	panswer = pmx;
	for (i=0; i < count_mx; i++) {
		if (i%2)
			printf("<tr class=td_data1>\n");
		else
			printf("<tr class=td_data2>\n");

		printf("<td class=td_data >%d</td>\n", i+1);
		printf("<td class=td_data ><input type=\"hidden\" name=\"mx_id_%d\" value=\"%d\">\n", i, (panswer?panswer->id_mx:0));
		printf("<input type=\"text\" maxlength=\"255\" id=\"mx_title_%d\" name=\"mx_title_%d\" value=\"%s\"></td>\n", i, i, (panswer?panswer->mx_title:""));
		printf("<td class=td_data ><input type=\"text\" maxlength=\"255\" id=\"mx_domain_%d\" name=\"mx_domain_%d\" value=\"%s\"></td>\n", i, i, (panswer?panswer->mx_domain_name:""));
		printf("<td class=td_data ><input type=\"text\" maxlength=\"15\" id=\"mx_ip_%d\" name=\"mx_ip_%d\" value=\"%s\"></td>\n", i, i, (panswer?panswer->mx_ip:""));
		printf("<td class=td_data ><input type=\"button\" value=\"��������\" onClick=\"delete_rec('mx' ,%d)\"></td>\n", i);
		printf("</tr>\n");
		if (panswer)
			panswer = panswer->next;
	}

	printf("</table>\n");
	printf("</form>\n");
};

void html_print_config_ns_mx_update()
{
	struct __p_answer * psql = NULL;
	char * id = NULL;
	char * domain = NULL;
	char * title = NULL;
	char * ip = NULL;
	char * ns_count = NULL;
	char * mx_count = NULL;
	char * temp = NULL;
	char * temp2 = NULL;
	unsigned int i = 0;

	ns_count = get_value("NS_COUNT");
	mx_count = get_value("MX_COUNT");

	if (!ns_count ||
		!mx_count) {
		
		printf("<span class=error>�� ������ ���������. ��������, ��� ������ ���������� �����.</span><br>\n");
		return;
	}

	for (i=0; i < atoi(ns_count); i++) {
		temp = strdup("NS_ID_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		id = get_value(temp);
		safe_free(&temp);

		temp = strdup("NS_TITLE_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		title = get_value(temp);
		safe_free(&temp);

		temp = strdup("NS_DOMAIN_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		domain = get_value(temp);
		safe_free(&temp);
		
		temp = strdup("NS_IP_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		ip = get_value(temp);
		safe_free(&temp);

		if (!id || !title || !domain || !ip) {
			printf("<span class=error>�� ������ ��������� NS ��������. ��������, ��� ������ ���������� �����.</span><br>\n");
			return;
		}

		if (strlen(id) == 0) {
			printf("<br><center>�� ������ ��������� NS �������� - ������ ����� ������ ���������� �����</center>\n");
			return;
		}
		
		if (strlen(title) == 0 && strlen(domain) == 0 && strlen(ip) == 0) {
			safe_free(&id);
			safe_free(&title);
			safe_free(&domain);
			safe_free(&ip);
			continue;
		}
		
		if (strlen(title) == 0 || strlen(domain) == 0 || strlen(ip) == 0) {
			printf("<br><center>�� ������ ��������� NS �������� - ������ ����� ������ ���������� �����</center>\n");
			return;
		}

		safe_free(&id);
		safe_free(&title);
		safe_free(&domain);
		safe_free(&ip);
	}

	for (i=0; i < atoi(mx_count); i++) {
		temp = strdup("MX_ID_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		id = get_value(temp);
		safe_free(&temp);

		temp = strdup("MX_TITLE_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		title = get_value(temp);
		safe_free(&temp);

		temp = strdup("MX_DOMAIN_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		domain = get_value(temp);
		safe_free(&temp);
		
		temp = strdup("MX_IP_");
		temp2 = int_to_string(i);
		temp = sts(&temp, temp2);
		if (temp2)
			safe_free(&temp2);
		ip = get_value(temp);
		safe_free(&temp);

		if (!id || !title || !domain || !ip) {
			printf("<span class=error>�� ������ ��������� MX ��������. ��������, ��� ������ ���������� �����.</span><br>\n");
			return;
		}

		if (strlen(id) == 0) {
			printf("<br><center>�� ������ ��������� MX �������� - ������ ����� ������ ���������� �����</center>\n");
			return;
		}
		
		if (strlen(title) == 0 && strlen(domain) == 0 && strlen(ip) == 0) {
			safe_free(&id);
			safe_free(&title);
			safe_free(&domain);
			safe_free(&ip);
			continue;
		}
		
		if (strlen(title) == 0 || strlen(domain) == 0 || strlen(ip) == 0) {
			printf("<br><center>�� ������ ��������� MX �������� - ������ ����� ������ ���������� �����</center>\n");
			return;
		}

		safe_free(&id);
		safe_free(&title);
		safe_free(&domain);
		safe_free(&ip);
	}
	
//*************************************************//
//*************************************************//

	psql = sql_execute("cfg_ns_mx_update_ns");
	if (psql->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", psql->errmsg, psql->source_query);
		return;
	}

	psql = sql_execute("cfg_ns_mx_update_mx");
	if (psql->result_code == SQL_RESULT_ERROR) {
		printf("<span class=error_simple>Caught Error: %s<br>Query: %s</span><br>", psql->errmsg, psql->source_query);
		return;
	}

//*************************************************//
//*************************************************//

	html_print_config_ns_mx();
};
