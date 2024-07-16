<template>
    <div class="hello">
      <h1>{{ msg }}</h1>
      <p>Open your Browser console to see the headers sent in by Laravel.</p>
    </div>
  </template>
  <script>
  export default {
    name: "HelloWorld",
    props: {
      msg: String,
    },
    mounted() {
      let req = new Request("http://localhost:8000/api/items", {
        method: "get",
      });
      fetch(req).then((response) => {
        for (let pair of response.headers.entries()) {
          console.log(pair[0] + ": " + pair[1]);
        }
      });
    },
  };
  </script>
  <!-- Add "scoped" attribute to limit CSS to this component only -->
  <style scoped>
  h3 {
    margin: 40px 0 0;
  }
  ul {
    list-style-type: none;
    padding: 0;
  }
  li {
    display: inline-block;
    margin: 0 10px;
  }
  a {
    color: #42b983;
  }
  </style>