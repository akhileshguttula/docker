# Use the official MySQL 8 image.
FROM mysql:8.0

# Copy the SQL initialization script into the directory that
# the MySQL image automatically executes on first initialization.
COPY init.sql /docker-entrypoint-initdb.d/01-init.sql

# MySQL listens on port 3306 inside the container.
EXPOSE 3306

# Root password, application database name and application DB user
# are supplied at runtime using docker run or Docker Compose.
