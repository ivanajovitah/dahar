@include('dashboard.sidenav')

<script>
    $("#generatePage").addClass("active");
</script>


<style>
    .titleMealPlan{
        font-size: 2.5rem;
        font-weight: 600;
    }
    #btnSubmit{
        width: 100%;
        margin-top: 50px;
    }
    .emoji{
        width: 40%;
    }
    .contentBody{
        padding: 5% 30%;
    }
    .thankyouTitle{
        font-family: 'Kaushan Script', cursive;
    }

    .rating-wrapper {
        align-self: center;
        display: inline-flex;
        direction: rtl !important;
        padding: 1.5rem 2.5rem;
        margin-left: auto;
    }
    .rating-wrapper label {
        color: #e1e6f6;
        cursor: pointer;
        display: inline-flex;
        font-size: 3rem;
        padding: 1rem 0.6rem;
        transition: color 0.5s;
    }
    .rating-wrapper svg {
        -webkit-text-fill-color: transparent;
        -webkit-filter: drop-shadow 4px 1px 6px #c6ceed;
        filter: drop-shadow(5px 1px 3px #c6ceed);
    }
    .rating-wrapper input {
        height: 100%;
        width: 100%;
    }
    .rating-wrapper input {
        display: none;
    }
    .rating-wrapper label:hover,
    .rating-wrapper label:hover ~ label,
    .rating-wrapper input:checked ~ label {
        color: #34ac9e;
    }
    .rating-wrapper label:hover,
    .rating-wrapper label:hover ~ label,
    .rating-wrapper input:checked ~ label {
        color: #34ac9e;
    }

    @media screen and (max-width: 810px) {
        .titleMealPlan{
            display: block;
        }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet"> 
<main>
    <div class="site-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <span class="titleMealPlan">Generate Meal Planner</span>
                </div>
            </div>

            <form method="POST" action="/generate-feedbackSave">
                @csrf 
                <div class="row contentBody">
                    <div class="col-sm-12" align="center">
                        <img class="emoji" src="assets/images/okEmoji.png">
                        <h1 class="thankyouTitle">Tersimpan !!</h1>
                        <h5>Sebarapa puas anda dengan hasil menu ?</h5>
                    </div>
                    <div class="col-sm-12" align="center">
                        <input type="text" value="{{$id_resultFeedback}}" name="id_resultFeedback" required hidden>
                        <div class="rating-wrapper">
            
                            <!-- star 5 -->
                            <input type="radio" id="5-star-rating" name="star-rating" value="5" required>
                            <label for="5-star-rating" class="star-rating">
                            <i class="fas fa-star d-inline-block"></i>
                            </label>
                            
                            <!-- star 4 -->
                            <input type="radio" id="4-star-rating" name="star-rating" value="4">
                            <label for="4-star-rating" class="star-rating star">
                            <i class="fas fa-star d-inline-block"></i>
                            </label>
                            
                            <!-- star 3 -->
                            <input type="radio" id="3-star-rating" name="star-rating" value="3">
                            <label for="3-star-rating" class="star-rating star">
                            <i class="fas fa-star d-inline-block"></i>
                            </label>
                            
                            <!-- star 2 -->
                            <input type="radio" id="2-star-rating" name="star-rating" value="2">
                            <label for="2-star-rating" class="star-rating star">
                            <i class="fas fa-star d-inline-block"></i>
                            </label>
                            
                            <!-- star 1 -->
                            <input type="radio" id="1-star-rating" name="star-rating" value="1">
                            <label for="1-star-rating" class="star-rating star">
                            <i class="fas fa-star d-inline-block"></i>
                            </label>
                            
                        </div>
                    </div>
                    <div class="col-sm-12" style="padding: 0;">
                        <button class="btn btn-info" id="btnSubmit" type="submit">
                            Save
                        </button>
                    </div>
                </div>
            </form>
            

            
        </div>
    </div>
</main>


@include('./footer')
