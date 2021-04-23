<template>
    <div>
      <div class="flex">
        <div class="input-row">
          <label>Make:</label>
          <select @change="onMakeChange" v-model="form.make" class="input--select">
            <option :value="null">--SELECT MAKE--</option>
            <option v-for="(make, key) in makes" :key="key" :value="make">{{make}}</option>
          </select>
        </div>
        <div class="input-row">
          <label>Model:</label>
          <select @change="onModelChange" :disabled="!form.make" v-model="form.model" class="input--select">
            <option :value="null">--SELECT MODEL--</option>
            <option v-for="(model, key) in models" :key="key" :value="model">{{model}}</option>
          </select>
        </div>
        <div class="input-row">
          <label>Year:</label>
          <select @change="onYearChange" placeholder="SELECT YEAR" :disabled="!form.make && !form.model" v-model="form.year" class="input--select">
            <option :value="null">--SELECT YEAR--</option>
            <option v-for="(year, key) in years" :key="key" :value="year">{{year}}</option>
          </select>
        </div>
      </div>
      <div class="test-results">
        <div class="text-center" v-if="!results.length">No results</div>
        <div v-if="results.length">
          <div v-for="result in results" :key="result.id">{{result.title}}</div>
        </div>
        
      </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      years: [],
      makes: [],
      
      models: [],
      form: {
        make: null,
        model: null,
        year: null
      },
      results: [],
      catId: 3
    }
  },
  mounted() {
    this.getMakes()
    this.getYears()
  },
  methods: {
    onMakeChange() {
      this.getModels()
      this.getYears()
      this.getArticles()
    },
    onModelChange() {
      this.getModels()
      this.getYears()
      this.getArticles()
    },
    onYearChange() {
      this.getArticles()
    },
    getYears() {
      let keys = Object.keys(this.form)
      let params = {
        cat_id: this.catId
      }
      keys.forEach(k => {
        if(this.form[k] && k !== 'year') {
          params[k] = this.form[k]
        }
      })

      axios.get('/api/ymm/options/years', {
        params: params
      }).then(res => {
        this.years = res.data
      })
    },
    getMakes() {
      let params = {
        cat_id: this.catId,
      }

      axios.get('/api/ymm/options/makes', {
        params: params
      }).then(res => {
        this.makes = res.data
      })
    },
    getModels() {
      let params = {
        cat_id: this.catId,
        make: this.form.make
      }
      axios.get('/api/ymm/options/models', {
        params: params
      }).then(res => {
        this.models = res.data
      })
    },
    getArticles() {
      let keys = Object.keys(this.form)
      let params = {}
      keys.forEach(k => {
        if(this.form[k]) {
          params[k] = this.form[k]
        }
      })
      console.log(params)
      axios.get('/api/search/articles', {
        params: params,
      })
      .then(res => {
        console.log(res)
        this.results = res.data.data
      })
    },
  },
  computed: {
    // currentModels() {
    //   let filtered = this.models.filter(m => m.make == this.form.make)
    //   return filtered.length ? filtered : []
    // }
  }
}
</script>

<style>

</style>