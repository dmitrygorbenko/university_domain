
BIN_DIR = /usr/local/sbin
ETC_DIR = /etc

PROGRAM = ns_control
FILES_TO_COMPILE = main str params message config tools dialog login dns domains zones zone_info serial
HEADERS_TO_COMPILE = define

CC = g++
CCFLAGS = -g -Wall
#CCFLAGS = -g -Wall -DFREEBSD
LDFLAGS =
C_SUFFIX = cpp
H_SUFFIX = h

HEADERS = $(addsuffix .$(H_SUFFIX), $(HEADERS_TO_COMPILE))
OBJS = $(addsuffix .o, $(FILES_TO_COMPILE))
FILES = $(addsuffix .$(C_SUFFIX), $(FILES_TO_COMPILE))

MAKEFLAGS = --no-print-directory --silent

all: compile-all link-all

compile-all: $(OBJS)
	echo "Compile done"

link-all: $(OBJS)
	$(CC) $(LDFLAGS) -o $(PROGRAM) $(OBJS)

%.o: %.$(C_SUFFIX) %.$(H_SUFFIX) $(HEADERS)
	$(CC) $(CCFLAGS) $(LDFLAGS) -c $< -o $@
	echo "$@ - done"

clean:
	rm -rf $(OBJS) $(PROGRAM)
	echo "Clean done"

install:
	mkdir -p $(BIN_DIR)
	cp -f $(PROGRAM) $(BIN_DIR)/$(PROGRAM)
	if [ ! -f $(ETC_DIR)/$(PROGRAM) ]; then \
		cp config $(ETC_DIR)/$(PROGRAM); \
	fi
	echo "Install done"

uninstall:
	rm -rf $(BIN_DIR)/$(PROGRAM)
	rm -rf $(ETC_DIR)/$(PROGRAM)
	echo "Uninstall done"

.NOEXPORT:
