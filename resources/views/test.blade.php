<!doctype html>
<html lang="fr" class="h-100" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Datatable</title>
    <!-- Favicon icon -->
    <!--link rel="icon" type="image/png" sizes="16x16" href="dist/img/cropped-favicon-noir-32x32.png"-->
    <!--link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3"-->
    <!-- JQUERY, doit être placé avant les scripts des plugins-->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- FONTAWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- DATATABLES -->
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.min.js"></script>
    <!-- DataTables style -->
    <link href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.min.css" rel="stylesheet">  
    <!-- DataTables plugins Styles-->
    <link href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.0.0/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/4.0.0/css/fixedHeader.dataTables.min.css" rel="stylesheet">  
    <!-- DataTables Plugins Scripts-->
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>
    <!-- Select2-->
    <!-- Custom-->

    <style>
      body {
  font: 90%/1.45em "Helvetica Neue", HelveticaNeue, Verdana, Arial, Helvetica, sans-serif;
  margin: 0;
  padding: 0;
  color: #333;
  background-color: #fff;
}
    </style>
  </head>

  <body class="d-flex flex-column h-100" style="padding-top: 0px;">

    <header>
    </header>

    <!-- Begin page content -->
    <main>
      <div class="container bg-light pb-5">

    

        <div class="row">
            <div class="col-md-12">

                <!--div class="card"-->
                    <!--div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        DataTable devrequests_table
                    </div-->
                    <!--div class="card-body"-->
                        <div class="table-responsive pb-5" style=" font-size: 15px;">
                            <table id="myTable" class="responsive nowrap hover row-border cell-border order-column compact" style="width:100%; background-color: white;">
                              <thead>
                                <tr>
                                  <th>Name</th>
                                  <th>Gender</th>
                                  <th>Position</th>
                                  <th>Office</th>
                                  <th>Age</th>
                                  <th class="none">Start date</th>
                                  <th class="none">Salary</th>
                                </tr>
                              </thead>

                              <tfoot>
                                <tr>
                                  <th>Name</th>
                                  <th>Gender</th>
                                  <th>Position</th>
                                  <th>Office</th>
                                  <th>Age</th>
                                  <th>Start date</th>
                                  <th>Salary</th>
                                </tr>
                              </tfoot>

                              <tbody>
                                <tr>
                                  <td>Tiger Nixon</td>
                                  <td>Male</td>
                                  <td>System Architect</td>
                                  <td>Edinburgh</td>
                                  <td>61</td>
                                  <td>2011/04/25</td>
                                  <td>$3,120</td>
                                </tr>
                                <tr>
                                  <td>Garrett Winters</td>
                                  <td>Male</td>
                                  <td>Director</td>
                                  <td>Edinburgh</td>
                                  <td>63</td>
                                  <td>2011/07/25</td>
                                  <td>$5,300</td>
                                </tr>
                                <tr>
                                  <td>Ashton Cox</td>
                                  <td>Male</td>
                                  <td>Technical Author</td>
                                  <td>San Francisco</td>
                                  <td>66</td>
                                  <td>2009/01/12</td>
                                  <td>$4,800</td>
                                </tr>
                                <tr>
                                  <td>Cedric Kelly</td>
                                  <td>Male</td>
                                  <td>Javascript Developer</td>
                                  <td>Edinburgh</td>
                                  <td>22</td>
                                  <td>2012/03/29</td>
                                  <td>$3,600</td>
                                </tr>
                                <tr>
                                  <td>Jenna Elliott</td>
                                  <td>Female</td>
                                  <td>Financial Controller</td>
                                  <td>Edinburgh</td>
                                  <td>33</td>
                                  <td>2008/11/28</td>
                                  <td>$5,300</td>
                                </tr>
                                <tr>
                                  <td>Brielle Williamson</td>
                                  <td>Female</td>
                                  <td>Integration Specialist</td>
                                  <td>New York</td>
                                  <td>61</td>
                                  <td>2012/12/02</td>
                                  <td>$4,525</td>
                                </tr>
                                <tr>
                                  <td>Herrod Chandler</td>
                                  <td>Male</td>
                                  <td>Sales Assistant</td>
                                  <td>San Francisco</td>
                                  <td>59</td>
                                  <td>2012/08/06</td>
                                  <td>$4,080</td>
                                </tr>
                                <tr>
                                  <td>Rhona Davidson</td>
                                  <td>Female</td>
                                  <td>Integration Specialist</td>
                                  <td>Edinburgh</td>
                                  <td>55</td>
                                  <td>2010/10/14</td>
                                  <td>$6,730</td>
                                </tr>
                                <tr>
                                  <td>Colleen Hurst</td>
                                  <td>Female</td>
                                  <td>Javascript Developer</td>
                                  <td>San Francisco</td>
                                  <td>39</td>
                                  <td>2009/09/15</td>
                                  <td>$5,000</td>
                                </tr>
                                <tr>
                                  <td>Sonya Frost</td>
                                  <td>Female</td>
                                  <td>Software Engineer</td>
                                  <td>Edinburgh</td>
                                  <td>23</td>
                                  <td>2008/12/13</td>
                                  <td>$3,600</td>
                                </tr>
                                <tr>
                                  <td>Jena Gaines</td>
                                  <td>Female</td>
                                  <td>System Architect</td>
                                  <td>London</td>
                                  <td>30</td>
                                  <td>2008/12/19</td>
                                  <td>$5,000</td>
                                </tr>
                                <tr>
                                  <td>Quinn Flynn</td>
                                  <td>Female</td>
                                  <td>Financial Controller</td>
                                  <td>Edinburgh</td>
                                  <td>22</td>
                                  <td>2013/03/03</td>
                                  <td>$4,200</td>
                                </tr>
                                <tr>
                                  <td>Charde Marshall</td>
                                  <td>Male</td>
                                  <td>Regional Director</td>
                                  <td>San Francisco</td>
                                  <td>36</td>
                                  <td>2008/10/16</td>
                                  <td>$5,300</td>
                                </tr>
                                <tr>
                                  <td>Haley Kennedy</td>
                                  <td>Male</td>
                                  <td>Senior Marketing Designer</td>
                                  <td>London</td>
                                  <td>43</td>
                                  <td>2012/12/18</td>
                                  <td>$4,800</td>
                                </tr>
                                <tr>
                                  <td>Tatyana Fitzpatrick</td>
                                  <td>Female</td>
                                  <td>Regional Director</td>
                                  <td>London</td>
                                  <td>19</td>
                                  <td>2010/03/17</td>
                                  <td>$2,875</td>
                                </tr>
                                <tr>
                                  <td>Michael Silva</td>
                                  <td>Male</td>
                                  <td>Senior Marketing Designer</td>
                                  <td>London</td>
                                  <td>66</td>
                                  <td>2012/11/27</td>
                                  <td>$3,750</td>
                                </tr>
                                <tr>
                                  <td>Paul Byrd</td>
                                  <td>Male</td>
                                  <td>Javascript Developer</td>
                                  <td>New York</td>
                                  <td>64</td>
                                  <td>2010/06/09</td>
                                  <td>$5,000</td>
                                </tr>
                                <tr>
                                  <td>Gloria Little</td>
                                  <td>Female</td>
                                  <td>Systems Administrator</td>
                                  <td>New York</td>
                                  <td>59</td>
                                  <td>2009/04/10</td>
                                  <td>$3,120</td>
                                </tr>
                                <tr>
                                  <td>Bradley Greer</td>
                                  <td>Male</td>
                                  <td>Software Engineer</td>
                                  <td>London</td>
                                  <td>41</td>
                                  <td>2012/10/13</td>
                                  <td>$3,120</td>
                                </tr>
                                <tr>
                                  <td>Dai Rios</td>
                                  <td>Male</td>
                                  <td>System Architect</td>
                                  <td>Edinburgh</td>
                                  <td>35</td>
                                  <td>2012/09/26</td>
                                  <td>$4,200</td>
                                </tr>
                                <tr>
                                  <td>Jenette Caldwell</td>
                                  <td>Female</td>
                                  <td>Financial Controller</td>
                                  <td>New York</td>
                                  <td>30</td>
                                  <td>2011/09/03</td>
                                  <td>$4,965</td>
                                </tr>
                                <tr>
                                  <td>Yuri Berry</td>
                                  <td>Female</td>
                                  <td>System Architect</td>
                                  <td>New York</td>
                                  <td>40</td>
                                  <td>2009/06/25</td>
                                  <td>$3,600</td>
                                </tr>
                                <tr>
                                  <td>Caesar Vance</td>
                                  <td>Male</td>
                                  <td>Technical Author</td>
                                  <td>New York</td>
                                  <td>21</td>
                                  <td>2011/12/12</td>
                                  <td>$4,965</td>
                                </tr>
                                <tr>
                                  <td>Doris Wilder</td>
                                  <td>Female</td>
                                  <td>Sales Assistant</td>
                                  <td>Edinburgh</td>
                                  <td>23</td>
                                  <td>2010/09/20</td>
                                  <td>$4,965</td>
                                </tr>
                                <tr>
                                  <td>Angelica Ramos</td>
                                  <td>Female</td>
                                  <td>System Architect</td>
                                  <td>London</td>
                                  <td>36</td>
                                  <td>2009/10/09</td>
                                  <td>$2,875</td>
                                </tr>
                                <tr>
                                  <td>Gavin Joyce</td>
                                  <td>Male</td>
                                  <td>Developer</td>
                                  <td>Edinburgh</td>
                                  <td>42</td>
                                  <td>2010/12/22</td>
                                  <td>$4,525</td>
                                </tr>
                                <tr>
                                  <td>Jennifer Chang</td>
                                  <td>Female</td>
                                  <td>Regional Director</td>
                                  <td>London</td>
                                  <td>28</td>
                                  <td>2010/11/14</td>
                                  <td>$4,080</td>
                                </tr>
                                <tr>
                                  <td>Brenden Wagner</td>
                                  <td>Female</td>
                                  <td>Software Engineer</td>
                                  <td>San Francisco</td>
                                  <td>18</td>
                                  <td>2011/06/07</td>
                                  <td>$3,750</td>
                                </tr>
                                <tr>
                                  <td>Ebony Grimes</td>
                                  <td>Female</td>
                                  <td>Software Engineer</td>
                                  <td>San Francisco</td>
                                  <td>48</td>
                                  <td>2010/03/11</td>
                                  <td>$2,875</td>
                                </tr>
                                <tr>
                                  <td>Russell Chavez</td>
                                  <td>Male</td>
                                  <td>Director</td>
                                  <td>Edinburgh</td>
                                  <td>20</td>
                                  <td>2011/08/14</td>
                                  <td>$3,600</td>
                                </tr>
                                <tr>
                                  <td>Michelle House</td>
                                  <td>Female</td>
                                  <td>Integration Specialist</td>
                                  <td>Edinburgh</td>
                                  <td>37</td>
                                  <td>2011/06/02</td>
                                  <td>$3,750</td>
                                </tr>
                                <tr>
                                  <td>Suki Burks</td>
                                  <td>Female</td>
                                  <td>Developer</td>
                                  <td>London</td>
                                  <td>53</td>
                                  <td>2009/10/22</td>
                                  <td>$2,875</td>
                                </tr>
                                <tr>
                                  <td>Prescott Bartlett</td>
                                  <td>Male</td>
                                  <td>Technical Author</td>
                                  <td>London</td>
                                  <td>27</td>
                                  <td>2011/05/07</td>
                                  <td>$6,730</td>
                                </tr>
                                <tr>
                                  <td>Gavin Cortez</td>
                                  <td>Male</td>
                                  <td>Technical Author</td>
                                  <td>San Francisco</td>
                                  <td>22</td>
                                  <td>2008/10/26</td>
                                  <td>$6,730</td>
                                </tr>
                                <tr>
                                  <td>Martena Mccray</td>
                                  <td>Female</td>
                                  <td>Integration Specialist</td>
                                  <td>Edinburgh</td>
                                  <td>46</td>
                                  <td>2011/03/09</td>
                                  <td>$4,080</td>
                                </tr>
                                <tr>
                                  <td>Unity Butler</td>
                                  <td>Male</td>
                                  <td>Senior Marketing Designer</td>
                                  <td>San Francisco</td>
                                  <td>47</td>
                                  <td>2009/12/09</td>
                                  <td>$3,750</td>
                                </tr>
                                <tr>
                                  <td>Howard Hatfield</td>
                                  <td>Male</td>
                                  <td>Financial Controller</td>
                                  <td>San Francisco</td>
                                  <td>51</td>
                                  <td>2008/12/16</td>
                                  <td>$4,080</td>
                                </tr>
                                <tr>
                                  <td>Hope Fuentes</td>
                                  <td>Female</td>
                                  <td>Financial Controller</td>
                                  <td>San Francisco</td>
                                  <td>41</td>
                                  <td>2010/02/12</td>
                                  <td>$4,200</td>
                                </tr>
                                <tr>
                                  <td>Vivian Harrell</td>
                                  <td>Female</td>
                                  <td>System Architect</td>
                                  <td>San Francisco</td>
                                  <td>62</td>
                                  <td>2009/02/14</td>
                                  <td>$4,965</td>
                                </tr>
                                <tr>
                                  <td>Timothy Mooney</td>
                                  <td>Male</td>
                                  <td>Financial Controller</td>
                                  <td>London</td>
                                  <td>37</td>
                                  <td>2008/12/11</td>
                                  <td>$4,200</td>
                                </tr>
                                <tr>
                                  <td>Jackson Bradshaw</td>
                                  <td>Male</td>
                                  <td>Director</td>
                                  <td>New York</td>
                                  <td>65</td>
                                  <td>2008/09/26</td>
                                  <td>$5,000</td>
                                </tr>
                                <tr>
                                  <td>Miriam Weiss</td>
                                  <td>Female</td>
                                  <td>Support Engineer</td>
                                  <td>Edinburgh</td>
                                  <td>64</td>
                                  <td>2011/02/03</td>
                                  <td>$4,965</td>
                                </tr>
                                <tr>
                                  <td>Bruno Nash</td>
                                  <td>Male</td>
                                  <td>Software Engineer</td>
                                  <td>London</td>
                                  <td>38</td>
                                  <td>2011/05/03</td>
                                  <td>$4,200</td>
                                </tr>
                                <tr>
                                  <td>Odessa Jackson</td>
                                  <td>Female</td>
                                  <td>Support Engineer</td>
                                  <td>Edinburgh</td>
                                  <td>37</td>
                                  <td>2009/08/19</td>
                                  <td>$3,600</td>
                                </tr>
                                <tr>
                                  <td>Thor Walton</td>
                                  <td>Male</td>
                                  <td>Developer</td>
                                  <td>New York</td>
                                  <td>61</td>
                                  <td>2013/08/11</td>
                                  <td>$3,600</td>
                                </tr>
                                <tr>
                                  <td>Finn Camacho</td>
                                  <td>Male</td>
                                  <td>Support Engineer</td>
                                  <td>San Francisco</td>
                                  <td>47</td>
                                  <td>2009/07/07</td>
                                  <td>$4,800</td>
                                </tr>
                                <tr>
                                  <td>Elton Baldwin</td>
                                  <td>Male</td>
                                  <td>Data Coordinator</td>
                                  <td>Edinburgh</td>
                                  <td>64</td>
                                  <td>2012/04/09</td>
                                  <td>$6,730</td>
                                </tr>
                                <tr>
                                  <td>Zenaida Frank</td>
                                  <td>Female</td>
                                  <td>Software Engineer</td>
                                  <td>New York</td>
                                  <td>63</td>
                                  <td>2010/01/04</td>
                                  <td>$4,800</td>
                                </tr>
                                <tr>
                                  <td>Zorita Serrano</td>
                                  <td>Female</td>
                                  <td>Software Engineer</td>
                                  <td>San Francisco</td>
                                  <td>56</td>
                                  <td>2012/06/01</td>
                                  <td>$5,300</td>
                                </tr>
                                <tr>
                                  <td>Jennifer Acosta</td>
                                  <td>Female</td>
                                  <td>Javascript Developer</td>
                                  <td>Edinburgh</td>
                                  <td>43</td>
                                  <td>2013/02/01</td>
                                  <td>$2,875</td>
                                </tr>
                                <tr>
                                  <td>Cara Stevens</td>
                                  <td>Female</td>
                                  <td>Sales Assistant</td>
                                  <td>New York</td>
                                  <td>46</td>
                                  <td>2011/12/06</td>
                                  <td>$4,800</td>
                                </tr>
                                <tr>
                                  <td>Hermione Butler</td>
                                  <td>Female</td>
                                  <td>Director</td>
                                  <td>London</td>
                                  <td>47</td>
                                  <td>2011/03/21</td>
                                  <td>$4,080</td>
                                </tr>
                                <tr>
                                  <td>Lael Greer</td>
                                  <td>Male</td>
                                  <td>Systems Administrator</td>
                                  <td>London</td>
                                  <td>21</td>
                                  <td>2009/02/27</td>
                                  <td>$3,120</td>
                                </tr>
                                <tr>
                                  <td>Jonas Alexander</td>
                                  <td>Male</td>
                                  <td>Developer</td>
                                  <td>San Francisco</td>
                                  <td>30</td>
                                  <td>2010/07/14</td>
                                  <td>$5,300</td>
                                </tr>
                                <tr>
                                  <td>Shad Decker</td>
                                  <td>Male</td>
                                  <td>Regional Director</td>
                                  <td>Edinburgh</td>
                                  <td>51</td>
                                  <td>2008/11/13</td>
                                  <td>$5,300</td>
                                </tr>
                                <tr>
                                  <td>Michael Bruce</td>
                                  <td>Male</td>
                                  <td>Javascript Developer</td>
                                  <td>Edinburgh</td>
                                  <td>29</td>
                                  <td>2011/06/27</td>
                                  <td>$4,080</td>
                                </tr>
                                <tr>
                                  <td>Donna Snider</td>
                                  <td>Female</td>
                                  <td>System Architect</td>
                                  <td>New York</td>
                                  <td>27</td>
                                  <td>2011/01/25</td>
                                  <td>$3,120</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    <!--/div-->
                <!--/div-->
            </div>
        </div>


      </div>
    </main>

    <footer>
    </footer>
  

    <script>
      
    $(document).ready(function() {
        // Define datatable
        var table = $('#myTable').DataTable( {
            dom: 'lrt',
            fixedHeader: {
                headerOffset: 0
            },
            
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            autoWidth: false,
            responsive: true // pour masquer des lignes par la classe="none"
        } );

    } );

    </script>

  </body>
</html>