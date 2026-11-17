FROM php:8.2-cli

# Install OpenJDK for AdvancedCalculator.java
RUN apt-get update && apt-get install -y default-jdk && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

# Pre-compile AdvancedCalculator.java into java/bin so calculations are instant
RUN mkdir -p java/bin && javac -encoding UTF-8 -d java/bin AdvancedCalculator.java

# Render provides the PORT environment variable (defaults to 10000)
EXPOSE 10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000}"]
