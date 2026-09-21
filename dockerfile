FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

RUN apt update 
RUN apt install wget unzip apache2 -y
RUN wget https://www.tooplate.com/zip-templates/2160_exhibit_studio.zip
RUN unzip 2160_exhibit_studio.zip
RUN cp -r 2160_exhibit_studio/* /var/www/html/
EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"] 

