# REST key-val store

### Getting Started

There are two methods for getting started with this repo.

#### Familiar with Git?
Checkout this repo, install dependencies, then run the process with the following:

```
> git clone https://github.com/sksaju/rest-key-value-store.git into your php server
> cd rest-key-value-store
> composer start
```

#### Not Familiar with Git?
Click [here](https://github.com/sksaju/rest-key-value-store/archive/master.zip) then download the .zip file.  Extract the contents of the zip file in your php server, then open your terminal, change to the project directory, and:

```
> composer start
```

### TEST CASE

#### GET /api/values
Get all the values of the store.

```response: {key1: value1, key2: value2, key3: value3...}```

#### GET /api/values?keys=key1,key2
Get one or more specific values from the store.

```response: {key1: value1, key2: value2}```

#### POST /api/values
Save a value in the store.

```request: {key1: value1, key2: value2..}
response: {key1: value1, key2: value2..}```

#### PATCH /api/values
Update a value in the store.

```request: {key1: value5, key2: value3..}
response: {key1: value5, key2: value3..}```
