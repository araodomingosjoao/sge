<template>
    <div class="col-xl-7 col-lg-8">
        <div class="text-center">
            <img src="/assets_velzon/images/error400-cover.png" alt="error img" class="img-fluid">
            <div class="mt-3">
                <h3 class="text-uppercase">Sorry, Page not Found 😭</h3>
                <p class="text-muted mb-4">The page you are looking for not available!</p>
                <router-link to="/dashboard" class="btn btn-success"><i class="mdi mdi-home me-1"></i>Back to home</router-link>
            </div>
        </div>
    </div><!-- end col -->
  </template>
  
  <script>
  export default {
    name: 'NotFound',
  };
  </script>
  
  <style scoped>
  .not-found {
    text-align: center;
    margin-top: 50px;
  }
  .not-found h1 {
    font-size: 5rem;
    color: #f06548;
  }
  .not-found p {
    font-size: 1.5rem;
    color: #555;
  }
  </style>
  