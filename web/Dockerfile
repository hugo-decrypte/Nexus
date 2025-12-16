FROM nginx:alpine

# curl pour healthcheck
RUN apk add --no-cache curl

COPY public /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]