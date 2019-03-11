#!/usr/bin/env bash
_locate() {
  [ $# -lt 1 ] && echo "Usage: $0 <filename>" && return $FALSE
  find /usr -name $1 | grep -m 1 $1
}
# export -f _locate
