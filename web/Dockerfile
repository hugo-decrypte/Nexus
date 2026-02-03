# Étape 1 : build de l'app Vue avec Vite
FROM node:20-alpine AS builder

WORKDIR /app

COPY public/package.json public/package-lock.json ./
RUN npm ci

COPY public/ ./
RUN npm run build

# Étape 2 : servir le build avec nginx
FROM nginx:alpine

RUN apk add --no-cache curl

COPY --from=builder /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
