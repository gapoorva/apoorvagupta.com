FROM mhart/alpine-node
EXPOSE 3000

# create repo
WORKDIR /apoorvagupta.com
COPY . .
RUN npm install


# CMD ['node', '/apoorvagupta.com/bin/www']
ENTRYPOINT npm start


