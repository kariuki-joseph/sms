export default class TableData{
    constructor(tableName){
        this.table = tableName
        return this.initializeTable();
    }
    initializeTable(){
      let data = [];
      let columns =[];
      let rows = [];

      $(this.table).find("thead tr th").each(function(){columns.push($(this).text())});
      $(this.table).find("tbody tr").each(function(){$(this).find("td").each(function(){rows.push($(this).text().trim())})});

       for(let i=0; i<rows.length; i += columns.length){
            let td = {};
          for(let j=0; j<columns.length-1; j++){
                td[columns[j]] = rows[i+j]
          }
          data.push(td)
       }

       return data;
    }
}
