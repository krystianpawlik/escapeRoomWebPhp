# escapeRoomWebPhp

# build
docker build -t php-dev-server .

# run linux
docker run -p 8000:8000 \
  -v $(pwd):/app \
  -w /app \
  --rm \
  php-dev-server


# run windows
docker run -p 8000:8000 -v ${PWD}:/app -w /app  --rm -it php-dev-server