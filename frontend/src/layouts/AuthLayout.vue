<template>
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
      <div class="bg-overlay"></div>
      <div class="auth-page-content overflow-hidden pt-lg-5">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="card overflow-hidden">
                <router-view></router-view>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'AuthLayout',
  };
  </script>
  
  <style scoped>
  </style>
  